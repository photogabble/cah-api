<?php

use App\Card;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;

class SeedCards extends Seeder
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * SeedCards constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $files = $this->filesystem->files(__DIR__.DIRECTORY_SEPARATOR.'decks');
        foreach ($files as $json){
            $this->importJson(json_decode($json->getContents()));
        }
    }

    /**
     * Import decks from https://crhallberg.com/cah/
     * @param stdClass $json
     * @throws Exception
     */
    private function importJson(stdClass $json){
        foreach ($json->order as $deckName){
            $deck = $json->{$deckName};
            $count = count($deck->black) + count($deck->white);
            $this->command->getOutput()->writeln( '[<info> + </info>] Importing card deck [ <comment>'.$deck->name.'</comment> ] containing <comment>[ '. $count .' ]</comment> cards:');
            $deckModel = new \App\Deck(['name' => $deck->name]);

            if (! $deckModel->save()){
                $this->command->getOutput()->writeln('[<error>!</error>] Unexpected issue while writing to database, cancelling job and exiting.');
                throw new Exception('Unable to save App\Deck model: ['. $deckModel->toJson() .']');
            }

            foreach ($deck->black as $id){
                $source = $json->blackCards[$id];
                $card = new Card(['type' => 'black', 'content' => $source->text, 'pick' => $source->pick]);
                $deckModel->cards()->save($card);
            }

            foreach ($deck->white as $id){
                $card = new Card(['type' => 'white', 'content' => $json->whiteCards[$id]]);
                $deckModel->cards()->save($card);
            }
        }

    }
}
