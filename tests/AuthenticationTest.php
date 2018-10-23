<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Class AuthenticationTest
 *
 * TDD: Tests should account for the following:
 *
 * Users should never have to register to be able to play the game. When a new player wants to play the game the
 * frontend app should request the /anon-register endpoint. That will generate a new User record and return a JWT for
 * the client to continue authenticating. The user will be given a pre-generated display name which they may change
 * at any time so long as it is unique to the user table.
 *
 * If the player wishes to "register" they only need provide their email address, we do not want to store any passwords,
 * users will be able to authenticate via use of emailed magic authentication links aka how Slack does so. The only PI
 * I feel comfortable collecting is email address and display names.
 *
 * + Anonymous accounts "timeout" and are deleted after 90 days.
 * + Accounts with no activity for 90 days are marked inactive and an email will be sent notifying that their account
 *   will be deleted 90 days from that date, they will be given the option to sign in, or delete their account
 *   from that email.
 * + Users should be able to delete their account at any time
 * + Users should be able to change their display name at any time, so long as the replacement is unique to the table
 * + Users should be able to change their email address, this will require the new email address to be validated before
 *   the old one is replaced. The old email address will be emailed a notification to say what has happened and to
 *   provide a pathway if the action was against the wishes of the authentic owner.
 * + Deleting a user account will erase the user record and set null any game-play related table rows user_id column
 *   this ensures the history function works but removes PI from the database
 * + A user account must be able to download a copy of the data we have on file for them, in the case of this game that
 *   will be their email address, display name and game history.
 *
 * The following meta data can be collected for a user:
 *
 * + display name (required)
 * + email address (optional)
 * + last_active_at (set during activity)
 * + auth_token (used for magic authentication)
 */
class AuthenticationTest extends TestCase
{
    public function testUnauthenticatedAccessResultsInError()
    {
        $this->get('/me');
        $this->assertResponseStatus(403);
    }

    public function testAnonymousAuthentication()
    {
        $this->get('/anon-register');
        $this->assertResponseOk();
        $json = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey('jwt', $json);
        $this->assertArrayHasKey('displayName', $json);

        $this->get('/me');
        $this->assertResponseOk();
        $me = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertTrue(is_null($me['email']));
        $this->assertEquals($json['displayName'], $me['displayName']);
    }

    public function testAnonymousAccountTimeoutDeletion()
    {
        // @todo test anonymous accounts are deleted after 90 days but their game data remains intact (only user record should be deleted)
    }

    public function testUpdateDisplayName()
    {
        $this->get('/anon-register');
        $this->assertResponseOk();
        $auth = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());

        $this->post('/me', ['displayName' => 'Mc. Test Unit'], ['Authorization' => sprintf('Bearer %s', $auth['jwt'])]);
        $this->assertResponseOk();
        $me = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertEquals('Mc. Test Unit', $me['displayName']);
    }

    public function testUpdateEmailAddress()
    {
        $this->get('/anon-register');
        $this->assertResponseOk();
        $auth = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());

        // Test Setting an Email Address
        // + Should Update Email address just fine
        $this->post('/me', ['email' => 'hello@example.com'], ['Authorization' => sprintf('Bearer %s', $auth['jwt'])]);
        $this->assertResponseOk();
        $me = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertEquals('hello@example.com', $me['email']);

        // Test Updating a set email address
        // + Should appear to update email address, while sending two notifications
        //   one to the new email address to validate it and one to the old email
        //   address to notify of change of email address
        // + Until the new email address is validated we keep both on record, once
        //   it is validated we replace the actual email address record with the new
        //   one, and null the replacement email address field.

        // @todo

        // Test deleting a set email address
        // + Should be disallowed, if a user wants to make their account anonymous
        //   they should delete it.

        // @todo
    }

    public function testMagicAuthentication()
    {
        // Setup account and set email address
        $this->get('/anon-register');
        $this->assertResponseOk();
        $auth = json_decode($this->response->getContent(), true);
        $this->post('/me', ['email' => 'hello@example.com'], ['Authorization' => sprintf('Bearer %s', $auth['jwt'])]);
        $this->assertResponseOk();

        // Knock on the door:
        // + This should prompt the backend to send an email with the
        //   "magic auth" link inside.
        // + The /login endpoint will return with a 100 char auth_token
        //   this is used to identify the login attempt and is included
        //   within a signed link that is emailed to the address on file.
        $this->post('/login', ['email' => 'hello@example.com']);
        $this->assertResponseOk();
        $auth = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('auth_token', $auth);

        // @todo test emails are "sent"

        // Poll for authentication:
        // + The frontend should poll /login/magic every three seconds
        //   with the `auth_token`. This endpoint will return empty
        //   until the emailed link has been clicked. Once done it
        //   will return a json response with just the jwt.
        $this->get('/login/magic', ['token' => $auth['auth_token']]);
        $this->assertResponseStatus(204);

        // @todo simulate clicking email link

        $this->get('/login/magic', ['token' => $auth['auth_token']]);
        $this->assertResponseOk();
        $auth = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey('jwt', $auth);

        $this->post('/me', ['email' => 'hello@example.com'], ['Authorization' => sprintf('Bearer %s', $auth['jwt'])]);
        $this->assertResponseOk();
    }

    public function testInactiveAccountsGetNotificationAfter90Days()
    {
        // @todo
    }

    public function testInactiveAccountsGetDeletedAfter180Days()
    {
        // @todo
    }

    public function testAccountDeletion()
    {
        // This is a "double-locked" endpoint.
        // + The first time the endpoint is requested it returns a 100 char
        //   "confirm" string. If then requested again that string the user
        //   record is deleted - permanently.
        // + Only the user table where PI is stored should be altered, all
        //   game play related tables should have their relevant user_id
        //   records nulled.

        // @todo
    }

    public function testAccountDownload()
    {
        // @todo
    }
}
