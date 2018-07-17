<?php

namespace App;

use Exception;
use Mni\FrontYAML\Parser;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

// @todo: Split this out into several classes plz
class EmailConverter
{
    private $converter;
    private $path;
    private $break = '----break----';

    private $content;
    private $splitFile;
    private $lead;
    private $sections;

    public function __construct(Parser $converter)
    {
        $this->converter = $converter;
        $this->path = base_path() . '/resources/emailcontent/';
        $this->cssPath = public_path('css/app.css');
    }

    public function convertFile($fileName)
    {
        return $this->convertString($this->getFile($fileName));
    }

    private function getFile($fileName)
    {
        $filePath = $this->path . $fileName;

        if (! file_exists($filePath)) {
            throw new Exception("File {$filePath} does not exist.");
        }

        return file_get_contents($filePath);
    }

    public function convertString($string)
    {
        $document = $this->converter->parse($string, false);
        $frontMatter = $document->getYaml();
        $this->content = $document->getContent();

        $lead = $this->getLead();
        $sections = $this->getSections();

        $sections[] = $this->makePostListSection($frontMatter);

        $view = view('email.content', ['lead' => $lead, 'sections' => $sections, 'skipCss' => true]);

        return $this->inlineStyles($view->render());
    }

    private function makePostListSection($frontMatter)
    {
        // @todo definitely move this external
        $content = [];

        $content = array_merge(
            app(\App\FMGS::class)->postsBetween($frontMatter['firstFMGSEpisode'], $frontMatter['lastFMGSEpisode']),
            $content
        );

        $content = array_merge(
            app(\App\TightenBlog::class)->postsBetween($frontMatter['firstTightenBlogSlug'], $frontMatter['lastTightenBlogSlug']),
            $content
        );

        $content = array_merge(
            app(\App\MyBlog::class)->postsBetween($frontMatter['firstBlogSlug'], $frontMatter['lastBlogSlug']),
            $content
        );

        return [
            'key' => 'postlist',
            'content' => $content
        ];
    }

    private function getSections()
    {
        $this->parseSections();

        return $this->sections;
    }

    private function getLead()
    {
        $this->parseSections();

        return $this->lead;
    }

    private function parseSections()
    {
        if (! $this->splitFile) {
            $this->splitFile = $this->prepAndConvertSections(
                $this->keySections(
                    $this->splitSections(
                        $this->content
                    )
                )
            );

            list($lead, $sections) = $this->extractLead($this->splitFile);
            $this->lead = $lead;
            $this->sections = $sections;
        }
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
            $return[] = [
                'key' => trim(strtok($section, "\n"), '--'),
                'content' => trim(preg_replace('/^.+\n/', '', $section))
            ];
        }

        return $return;
    }

    private function prepAndConvertSections($sections)
    {
        return array_map(function ($section) {
            $section['content'] = $this->prepAndConvertSection($section['key'], $section['content']);
            return $section;
        }, $sections);
    }

    private function prepAndConvertSection($type, $content)
    {
        // @todo: Make this not a switch--instead register processors by type key
        switch ($type) {
            case 'columns':
                return $this->splitBy('column', $content);
                break;
            case 'postlist':
                return $this->splitBy('post', $content);
                break;
            case 'lead':
                return $this->convertLead($content);
                break;
            default:
                return $this->convertMdToHtml($content);
        }
    }

    private function extractLead($sections)
    {
        $return = [];

        foreach ($sections as $section) {
            if ($section['key'] == 'lead') {
                $lead = $section;
                continue;
            }

            $return[] = $section;
        }

        return [
            $lead,
            $return
        ];
    }

    private function convertLead($content)
    {
        // @todo There's gotta be a better way to do this
        $html = $this->convertMdToHtml($content);
        return str_replace(['<p>', '</p>'], ['', ''], $content);
    }

    private function splitBy($key, $content)
    {
        $chunks = explode("--" . $key . "--", trim($content));
        array_shift($chunks);

        return array_map(function ($chunk) {
            return $this->convertMdToHtml(trim($chunk));
        }, $chunks);
    }

    private function convertMdToHtml($markdown)
    {
        $document = $this->converter->parse($markdown);
        return $document->getContent($markdown);
    }

    private function inlineStyles($html)
    {
        $css = file_get_contents(public_path('css/zurb.css'));
        $css .= file_get_contents(public_path('css/custom.css'));

        return (new CssToInlineStyles)->convert($html, $css);
    }
}
