<?php

namespace App\Console\Commands\CommonVote;

use Illuminate\Console\Command;

class Cvote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wanjia:make:cvote {name : the name of the vote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $directories = [];
    protected $namespace;
    protected $views = [
        'index.stub' => 'index.html',
        'details.stub' => 'details.html',
        'register.stub' => 'register.html',
        'ranking.stub' => 'ranking.html',
        'rules.stub' => 'rules.html'
    ];

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
        $name = $this->input->getArgument('name');
        $this->checkDirectories($name);
        try {
            $this->checkDirectories($name);
            $this->createSource();
            $this->createLayout();
            $this->createViews();
            $this->info('创建成功');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }

        //
    }

    protected function checkDirectories($name)
    {
        $this->name = strtolower($name);

        $this->directories['layouts'] = resource_path("views/vendor/zhuanti/{$name}");
        $this->directories['source'] = app()->basePath().'/src/'.ucfirst($name);
        $this->directories['views'] = public_path("web/{$name}");

        $exist = [];
        foreach ($this->directories as $key => $directory) {
            if (is_dir($directory)) {
                $exist[] = $directory;
            }
        }

        if (!empty($exist)) {
            throw new \Exception(implode(PHP_EOL, $exist));
        }

    }

    protected function createSource()
    {
        mkdir($this->directories['source']);
        $controllersDir = $this->directories['source'].'/Controllers';
        $routesDir = $this->directories['source'].'/routes';
        mkdir($controllersDir);
        mkdir($routesDir);
        file_put_contents($controllersDir.'/Controller.php',
            str_replace('{{namespace}}', ucfirst($this->name), file_get_contents(__DIR__.'/controllers/Controller.stub')));
        $this->info('Created: '.$controllersDir.'/Controller.php');

        file_put_contents(
            $routesDir.'/default.php',
            str_replace('{{name}}', $this->name, file_get_contents(__DIR__.'/routes/default.stub'))
            );
        $this->info('Created: '.$routesDir.'/default.php');

    }

    protected function createLayout()
    {
        mkdir($this->directories['layouts']);
        file_put_contents($this->directories['layouts'].'/layout.blade.php',
            str_replace('{{name}}', $this->name, file_get_contents(__DIR__.'/layouts/layout.blade.stub')));
        $this->info('Created: '.$this->directories['layouts'].'/layout.blade.php');

    }

    protected function createViews()
    {
        mkdir($this->directories['views']);
        foreach ($this->views as $key => $view) {
            file_put_contents($this->directories['views'].'/'.$view,
                str_replace('{{name}}', $this->name, file_get_contents(__DIR__.'/views/'.$key)));
            $this->info('Created: '.$this->directories['views'].'/'.$view);
        }

    }
}
