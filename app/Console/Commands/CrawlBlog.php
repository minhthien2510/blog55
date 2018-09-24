<?php

namespace App\Console\Commands;

use App\Category;
use App\CategoryPost;
use App\Post;
use DiDom\Document;
use Illuminate\Console\Command;

class CrawlBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:crawlToDB';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl posts of the itviec blog to DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $itviecBlog = ['itviec' => 'https://itviec.com/blog/'];

        $page        = 1;
        $url         = $itviecBlog['itviec'] . 'page/' . $page;
        $domDocument = new \DOMDocument();
        $document    = new Document();
        $scrape      = new Scrape();

        // Could load the blog page
        while (@$domDocument->loadHTMLFile($url)) {
            $document->loadHtml($domDocument->saveHTML());

            if ($document->has('.post.teaser')) {
                $posts = $document->find('.post.teaser');

                foreach ($posts as $post) {
                    $postUrl = $itviecBlog['itviec'] . $scrape->getPathName($post);

                    // Could not load the blog post
                    if (!@$domDocument->loadHTMLFile($postUrl)) {
                        continue;
                    }

                    // If post is exist in DB
                    if (count(Post::where('name', $scrape->getPathName($post))->get()) > 0) {
                        continue;
                    }

                    $query1 = new Post();
                    $query1->title = $scrape->getTitle($post);
                    $query1->name = $scrape->getPathName($post);
                    $query1->content = $scrape->getContent($postUrl);
                    $query1->excerpt = $scrape->getSummaryContent($post);
                    $query1->image = $scrape->getThumbnail($post, $scrape->getPathName($post));
                    $query1->save();

                    foreach ($scrape->getCategories($post) as $key => $value) {
                        // If category doesn't exist
                        if (count(Category::where('slug', $key)->get()) == 0) {
                            Category::insert([
                                'slug' => $key,
                                'name' => $value
                            ]);
                        }

                        $query2 = Category::where('slug', $key)->first();

                        $query3 = new CategoryPost();
                        $query3->post_id = $query1->id;
                        $query3->category_id = $query2->id;
                        $query3->save();
                    }
                }
            }

            $page++;

            $url = $itviecBlog['itviec'] . 'page/' . $page;
        }

        $this->info("Success.");
    }
}
