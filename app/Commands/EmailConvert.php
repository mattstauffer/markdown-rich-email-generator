<?php namespace App\Commands;

use App\Commands\Command;
use Exception;
use Illuminate\Contracts\Bus\SelfHandling;
use League\CommonMark\CommonMarkConverter;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

// @todo: Split this out into several classes plz
class EmailConvert extends Command implements SelfHandling
{
    private $fileName;
    private $converter;
    private $path;
    private $break = '----break----';

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->path = base_path() . '/resources/emailcontent/';
    }

    // @todo: This version only allows for one of each type. We need to re-work it so that's not true. Ugh.
    public function handle(CommonMarkConverter $converter)
    {
        $this->converter = $converter;

        $file = $this->getFile($this->fileName);

        $sections = $this->splitSections($file);
        $sections = $this->keySections($sections);
        $sections = $this->prepAndConvertSections($sections);
        list($lead, $sections) = $this->extractLead($sections);

        $view = view('email.content', ['lead' => $lead, 'sections' => $sections]);

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

    private function prepAndConvertSections($sections)
    {
        $return = [];

        foreach ($sections as $key => $content) {
            $return[$key] = $this->prepAndConvertSection($key, $content);
        }

        return $return;
    }

    private function prepAndConvertSection($type, $content)
    {
        // @todo: Make this not a switch--instead register processors by type key
        switch ($type) {
            case 'columns':
                return $this->splitColumns($content);
                break;
            case 'lead':
                return $this->convertLead($content);
                break;
            default:
                return $this->convertMdToHtml($content);
        }
    }

    private function convertLead($content)
    {
        // @todo There's gotta be a better way to do this
        $html = $this->convertMdToHtml($content);
        return str_replace(['<p>', '</p>'], ['', ''], $content);
    }

    private function splitColumns($content)
    {
        $columns = explode("--column--", trim($content));
        array_shift($columns);

        return array_map(function ($column) {
            return $this->convertMdToHtml(trim($column));
        }, $columns);
    }

    private function extractLead($sections)
    {
        $lead = $sections['lead'];
        unset($sections['lead']);

        return [
            $lead,
            $sections
        ];
    }

    private function convertMdToHtml($markdown)
    {
        return $this->converter->convertToHtml($markdown);
    }

    private function inlineStyles($html)
    {
        $cssToInlineStyles = new CssToInlineStyles($html);
        $cssToInlineStyles->setUseInlineStylesBlock(true);
        return $cssToInlineStyles->convert();
    }
}
