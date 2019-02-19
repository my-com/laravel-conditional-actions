<?php

namespace ConditionalActions\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class ConditionalActionsTable extends Command
{
    protected $signature = 'ca:tables
        {--migrations-path=database/migrations : Path to migrations directory (relative to framework base path)}
    ';

    protected $description = 'Create a migration for the conditional actions database tables';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * Create a new failed queue jobs table command instance.
     *
     * @param  Filesystem $files
     * @param  Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $path = $this->laravel->basePath($this->option('migrations-path'));
        $tables = ['conditions', 'condition_actions'];

        foreach ($tables as $i => $table) {
            // Sleep needs to correct migrations order
            if ($i > 0) {
                \sleep(1);
            }
            $filename = $this->replaceMigration(
                $this->createBaseMigration($table, $path),
                $table,
                Str::studly($table)
            );

            $fileName = \trim(Str::after($filename, $path), '/');
            $this->line(\sprintf('<info>Migration created:</info> %s', $fileName));
        }

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a base migration file for the table.
     *
     * @param  string $table
     * @param string $path
     *
     * @return string
     */
    protected function createBaseMigration(string $table, string $path)
    {
        return $this->laravel['migration.creator']->create(
            'create_' . $table . '_table',
            $path
        );
    }

    /**
     * Replace the generated migration with the table stub.
     *
     * @param  string $path
     * @param  string $table
     * @param  string $tableClassName
     *
     * @throws FileNotFoundException
     *
     * @return string
     */
    protected function replaceMigration($path, $table, $tableClassName)
    {
        $stub = \str_replace(
            ['{{table}}', '{{tableClassName}}'],
            [$table, $tableClassName],
            $this->files->get(__DIR__ . "/stubs/{$table}.stub")
        );

        $this->files->put($path, $stub);

        return $path;
    }
}
