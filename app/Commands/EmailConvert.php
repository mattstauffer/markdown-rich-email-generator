<?php namespace App\Commands;

use App\Commands\Command;
use Exception;
use Illuminate\Contracts\Bus\SelfHandling;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class EmailConvert extends Command implements SelfHandling
{
    private $fileName;
    private $path;
    private $break = '----break----';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($fileName)
    {
        $this->path = base_path() . '/resources/emailcontent/';
        $this->fileName = $fileName;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $file = $this->getFile($this->fileName);

        $sections = $this->splitSections($file);
        $sections = $this->keySections($sections);
        $sections = $this->convertSectionsToHtml($sections);

        $view = view('email.content', ['sections' => $sections]);

        return $this->inlineStyles($view->render());
    }

    private function getFile($fileName)
    {
        $filePath = $this->path . $fileName;

        if (! file_exists($filePath)) {
            throw new Exception("File {$filePath} does not exist.");
        }

        return file_get_contents($filePath);
    }

    private function splitSections($markdown)
    {
        return array_map(function ($section) {
            return trim($section);
        }, explode($this->break, $markdown));
    }

    private function convertSectionsToHtml($sections)
    {
        $return = [];

        foreach ($sections as $key => $content) {
            $return[$key] = $this->convertMdToHtml($content);
        }

        return $return;
    }

    private function keySections($sections)
    {
        $return = [];

        foreach ($sections as $section) {
            // trim first line, make it key, set body to old body minus first line
            $key = trim(strtok($section, "\n"), '--');
            $return[$key] = trim(preg_replace('/^.+\n/', '', $section));
        }

        return $return;
    }

    private function convertMdToHtml($markdown)
    {
        return '<i>fake html conversion here</i>' . $markdown;
    }

    private function inlineStyles($html)
    {
        $cssToInlineStyles = new CssToInlineStyles($html);
        $cssToInlineStyles->setUseInlineStylesBlock(true);
        return $cssToInlineStyles->convert();
    }
}
