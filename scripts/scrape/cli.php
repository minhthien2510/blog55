<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/cache/config.php';
require_once 'Scrape.php';

use DiDom\Document;

libxml_use_internal_errors(true);

$blogUrls = array(
    'itviec'    => 'https://itviec.com/blog/'
);

foreach ($blogUrls as $siteName => $blogUrl) {
    $page        = 1;
    $url         = $blogUrl . 'page/' . $page;
    $domDocument = new DOMDocument();
    $document    = new Document();
    $scrape      = new Scrape();
    $result      = array();

    // Could load the blog page
    while ($domDocument->loadHTMLFile($url)) {
        echo "$url.\n";
        $document->loadHtml($domDocument->saveHTML());

        if ($document->has('.post.teaser')) {
            $posts = $document->find('.post.teaser');

            foreach ($posts as $post) {
                $postUrl = $blogUrl . $scrape->getPathName($post);
                echo "$postUrl.\n";

                // Could load the blog post
                if ($domDocument->loadHTMLFile($postUrl)) {
                    array_push($result, array(
                        'title' => $scrape->getTitle($post),
                        'name' => $scrape->getPathName($post),
                        'categories' => $scrape->getCategories($post),
                        'thumbnail' => $scrape->getThumbnail($post, $scrape->getPathName($post)),
                        'excerpt' => $scrape->getSummaryContent($post),
                        'content' => $scrape->getContent($postUrl)
                    ));
                }
            }

            $page++;
            if ($page == 3) {break;}
            $url = $blogUrl . 'page/' . $page;
        }
    }

    // Create a json file for blog site
    file_put_contents("public/$siteName-blog-content.json", json_encode($result, JSON_PRETTY_PRINT));
}
