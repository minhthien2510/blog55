<?php

namespace App\Console\Commands;
require_once 'app/helpers.php';

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
    protected $signature = 'crawl';

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
        $blogs = ['itviec' => 'https://itviec.com/blog/'];
        $page  = 1;
        $url   = $blogs['itviec'] . 'page/' . $page;

        // Could load the blog page
        while (is_load($url)) {
            $document = new Document($url, true);

            if ($document->has('.post.teaser')) {
                $posts = $document->find('.post.teaser');

                foreach ($posts as $post) {
                    $postUrl = $blogs['itviec'] . get_path_name($post);

                    // Could not load the blog post
                    if (! is_load($postUrl)) {
                        continue;
                    }

                    // If post is exist in DB
                    if (Post::where('name', get_path_name($post))->exists()) {
                        continue;
                    }

                    $postDB = new Post();
                    $postDB->title = get_title($post);
                    $postDB->name = get_path_name($post);
                    $postDB->excerpt = get_excerpt($post);
                    $postDB->content = get_content($postUrl);
                    $postDB->image = get_image($post, get_path_name($post));
                    $postDB->save();

                    foreach (get_categories($post) as $key => $value) {
                        // If category doesn't exist
                        if (! Category::where('slug', $key)->exists()) {
                            $categoryDB = new Category();
                            $categoryDB->name = $value;
                            $categoryDB->slug = $key;
                            $categoryDB->save();
                        }

                        $categoryDB = Category::where('slug', $key)->first();

                        $categoryPostDB = new CategoryPost();
                        $categoryPostDB->post_id = $postDB->id;
                        $categoryPostDB->category_id = $categoryDB->id;
                        $categoryPostDB->save();
                    }
                }
            }

            $page++;
            $url = $blogs['itviec'] . 'page/' . $page;
        }

        $this->info("Success.");
    }
}
