<?php

use DiDom\Document;
use DiDom\Element;

if (! function_exists('download_image')) {
    /**
     * Download the image to local directory
     *
     * @param string $directory
     * @param string $file
     * @param string $imgSrc
     * @return bool|int The function returns the number of bytes that were written to the file, or
     * false on failure.
     */
    function download_image($directory, $file, $imgSrc)
    {
        if (! is_load($imgSrc)) {
            return false;
        }

        // Could not load the image file
        if (! getimagesize($imgSrc)) {
            return false;
        }

        if (! is_dir($directory) && ! mkdir($directory, 0777, true)) {
            return false;
        }

        if (! is_file($file)) {
            return file_put_contents($file, file_get_contents($imgSrc));
        }

        return true;
    }
}

if (! function_exists('is_load')) {
    /**
     * @param string $url
     * @return bool
     */
    function is_load($url)
    {
        if ($url === '//itviec.com/blog/wp-content/plugins/a3-lazy-load/assets/images/lazy_placeholder.gif') {
            return false;
        }
        $headers = get_headers($url);
        return $headers[0] !== 'HTTP/1.1 404 Not Found' ? true : false;
    }
}

if (! function_exists('get_categories')) {
    /**
     * Get categories of the post
     *
     * @param  Element $post
     * @return array
     */
    function get_categories(Element $post)
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
}

if (! function_exists('get_content')) {
    /**
     * Get content of the post
     *
     * @param string $url
     * @return string
     */
    function get_content($url)
    {
        if (!is_load($url)) {
            return '';
        }

        $document = new Document($url, true);

        if (! $document->has('.post .content')) {
            return '';
        }

        $blockquotes = $document->find('blockquote');
        foreach ($blockquotes as $blockquote) {
            $blockquote->remove();
        }

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

                if (download_image($directory, "$directory/$imgName", $src)) {
                    $image->setAttribute('src', "public/media/blog/$urlKey/$imgName");
                } else {
                    $image->setAttribute('src', '');
                }
            }
        }

        $content = trim($document->first('.content')->innerHtml());

        return $content;
    }
}

if (! function_exists('get_excerpt')) {
    /**
     * Get excerpt of the post
     *
     * @param  Element $post
     * @return string
     */
    function get_excerpt(Element $post)
    {
        $summaryContent = '';

        if ($post->has('.content')) {
            $document = new Document($post->first('.content')->html());

            if ($document->has('img')) {
                $document->first('img')->remove();
            }

            if ($document->has('.more-link')) {
                $document->first('.more-link')->parent()->remove();
            }

            $summaryContent = trim($document->first('.content')->innerHtml());
        }

        return $summaryContent;
    }
}

if (! function_exists('get_image')) {
    /**
     * Get thumbnail of the post
     *
     * @param  Element $post
     * @param string $urlKey
     * @return string
     */
    function get_image(Element $post, $urlKey)
    {
        $image = '';

        if ($post->has('.content img')) {
            $imgSrc = $post->first('.content img')->getAttribute('src');
            $directory = "public/media/blog/$urlKey";
            $imgName = basename($imgSrc);

            if (download_image($directory, "$directory/$imgName", $imgSrc)) {
                $image = "$directory/$imgName";
            }
        }

        return $image;
    }
}

if (! function_exists('get_path_name')) {
    /**
     * Get url path of the post
     *
     * @param  Element $post
     * @return string
     */
    function get_path_name(Element $post)
    {
        $urlPath = '';

        if ($post->has('.title a')) {
            $urlPath = basename($post->first('.title a')->getAttribute('href'));
        } elseif ($post->has('.more-link')) {
            $urlPath = basename($post->first('.more-link')->getAttribute('href'));
        }

        return $urlPath;
    }
}

if (! function_exists('get_title')) {
    /**
     * Get title of the post
     *
     * @param  Element $post
     * @return string
     */
    function get_title(Element $post)
    {
        $title = '';

        if ($post->has('.title')) {
            $title = trim($post->first('.title')->text());
        }

        return $title;
    }
}
