<?php

namespace App\Console\Commands;

use App\Repositories\Spintax;
use App\Word;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Wordai extends Command
{
	private $article;
	private $spin;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordai:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a process for wordai api.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Spintax $spin)
    {
        parent::__construct();
		$this->spin = $spin;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$this->process_api();
    }

    /**
     * Unprocess articles
     * 
     * @return [type] [description]
     */
    protected function has_unprocess_article()
	{
		$this->article = Word::where(['isUserEdit' => 0, 'isProcess' => 0])->first();

		return count($this->article) > 0 ? true : false;
	}

	/**
	 * Wordai api
	 * 
	 * @return [type] [description]
	 */
	protected function api()
	{
		// article object
		$article = $this->article;

		$text = $article->article;
		$quality = 100;
		$email = 'accounting@connexionsolutions.com';
		$pass = 'fastredsportscar';
		$protected = $article->protected;
		$synonyms = $article->synonym;

		$quality = 'readable';
		$nonested = 'off';
		$sentence = 'on';
		$paragraph = 'on';
		$title = 'off';
		$nooriginal = 'on';

		if (isset($text) && isset($quality) && isset($email) && isset($pass))
		{
			$text = urlencode($text);
			$ch = curl_init('http://wordai.com/users/turing-api.php');

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, "s=$text&quality=$quality&email=$email&pass=$pass&output=json&protected=$protected&synonyms=$synonyms");
			
			$result = curl_exec($ch);
			curl_close ($ch);

			return $result;
		}
		else
		{
			return 'Error: Not All Variables Set!';
		}
	}

	/**
	 * Spin result after spintax
	 * 
	 * @param  [type] $article [description]
	 * @return [type]          [description]
	 */
	protected function spinArticleResult($article)
	{
		return $this->spin->process($article);
	}

	/**
	 * Process wordai api
	 * 
	 * @return [type] [description]
	 */
	private function process_api()
	{
		if ($this->has_unprocess_article()) { 		// if there is unprocess article
			$result = json_decode($this->api()); 	// decode json result from the response of server
			$status = $result->status;

			if ($status === 'Success') {
				$spintax = $result->text;
				$articleObj = $this->article;
				$word_id = $articleObj->id;
				$spin = $this->spinArticleResult($spintax); 	// spin the spintax result

				// update words table
				Word::where('id', $word_id)->update(['spintax' => $spintax, 'spin' => $spin, 'isProcess' => 1]);
			}
			
			// Log the response status result
			Log::info($status);
		}
	}
}
