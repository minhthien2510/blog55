<?php

use DiDom\Document;
use DiDom\Element;

class Scrape
{
    /**
     * Get categories of the post
     *
     * @param Element $post
     * @return array
     */
    public function getCategories(Element $post)
    {
        $categories = array();

        if ($post->has('.cats')) {
            $cats = $post->find('.cats a');
            foreach ($cats as $cat) {
                $categories[basename($cat->getAttribute('href'))] = $cat->text();
            }
        }

        return $categories;
    }

    /**
     * Get content of the post
     *
     * @param string $siteName
     * @param string $url
     * @return string
     */
    public function getContent($url)
    {
        $document = new Document($url, true);

        if (!$document->has('.post .content')) {
            return '';
        }

//        if (count($elements = $document->find('blockquote')) > 0) {
//            for ($i = 0; $i < count($elements); $i++) {
//                $document->first('blockquote')->remove();
//            }
//        }

        if ($document->has('.content .tve-leads-post-footer')) {
            $document->first('content .tve-leads-post-footer')->remove();
        }

        if ($document->has('.content .swp_social_panel')) {
            $document->first('.content .swp_social_panel')->remove();
        }

        if ($document->has('.content .jp-relatedposts')) {
            $document->first('.content .jp-relatedposts')->remove();
        }

        if (count($images = $document->find('.content img')) > 0) {
            foreach ($images as $image) {
                $image->removeAttribute('alt')
                    ->removeAttribute('width')
                    ->removeAttribute('height')
                    ->removeAttribute('srcset')
                    ->removeAttribute('sizes');

                $urlKey = basename($url);
                $src = strtok($image->getAttribute('src'), '?');
                $directory = "public/media/blog/$urlKey";
                $imgName = basename($src);

                if ($this->downloadImage($directory, "$directory/$imgName", $src)) {
                    $image->setAttribute('src', "public/media/blog/$urlKey/$imgName");
                } else {
                    $image->setAttribute('src', '');
                }
            }
        }

        $content = trim($document->first('.content')->innerHtml());

        return $content;
    }

    /**
     * Get summary content of the post
     *
     * @param Element $post
     * @return string
     */
    public function getSummaryContent(Element $post)
    {
        if (!$post->has('.content')) {
            return '';
        }

        $document = new Document($post->first('.content')->html());

        if ($document->has('img')) {
            $document->first('img')->remove();
        }

//        if ($document->has('a[target="_blank"]')) {
//            $document->first('a[target="_blank"]')->parent()->remove();
//        }

        if ($document->has('.more-link')) {
            $document->first('.more-link')->parent()->remove();
        }

        $summaryContent = trim($document->first('.content')->innerHtml());

        return $summaryContent;
    }

    /**
     * Get thumbnail of the post
     *
     * @param Element $post
     * @param string $siteName
     * @param string $urlKey
     * @return string
     */
    public function getThumbnail(Element $post, $urlKey)
    {
        if (!$post->has('.content img')) {
            return '';
        }

        $imgSrc = $post->first('.content img')->getAttribute('src');
        $directory = "public/media/blog/$urlKey";
        $imgName = basename($imgSrc);

        $thumbnail = '';
        if ($this->downloadImage($directory, "$directory/$imgName", $imgSrc)) {
            $thumbnail = "public/media/blog/$urlKey/$imgName";
        }

        return $thumbnail;
    }

    /**
     * Get title of the post
     *
     * @param Element $post
     * @return string
     */
    public function getTitle(Element $post)
    {
        if (!$post->has('.title')) {
            return '';
        }

        $title = trim($post->first('.title')->text());

        return $title;
    }

    /**
     * Get url of the post
     *
     * @param Element $post
     * @return string
     */
    public function getPathName(Element $post)
    {
        $urlPath = '';

        if ($post->has('.title a')) {
            $urlPath = basename($post->first('.title a')->getAttribute('href'));
        } elseif ($post->has('.more-link')) {
            $urlPath = basename($post->first('.more-link')->getAttribute('href'));
        }

        return $urlPath;
    }

    /**
     * Download the image to local directory
     *
     * @param string $directory
     * @param string $file
     * @param string $imgSrc
     * @return bool|int The function returns the number of bytes that were written to the file, or
     * false on failure.
     */
    private function downloadImage($directory, $file, $imgSrc)
    {
        // Could not load the image file
        if (!getimagesize($imgSrc)) {
            return false;
        }

        if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
            return false;
        }

        if (!is_file($file)) {
            return file_put_contents($file, file_get_contents($imgSrc));
        }

        return true;
    }
}