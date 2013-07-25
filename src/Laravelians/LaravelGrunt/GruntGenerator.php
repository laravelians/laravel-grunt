<?php namespace Laravelians\LaravelGrunt;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

class GruntGenerator {

	/**
	 * Filesystem
	 * 
	 * @var Illuminate\Filesystem
	 */
	protected $filesystem;

	/**
	 * GruntFile
	 * 
	 * @var Laravelians\LaravelGrunt\Gruntfile
	 */
	protected $gruntFile;

	/**
	 * Config Instance
	 * 
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Constructor
	 * 
	 * @param Filesystem $filesystem
	 * @param Gruntfile  $gruntFile 
	 */
	public function __construct(Filesystem $filesystem, Gruntfile $gruntFile, Config $config)
	{
		$this->filesystem = $filesystem;
		$this->gruntFile = $gruntFile;
		$this->config = $config;
	}

	/**
	 * Create a gruntfile.js file
	 * 
	 * @param  array  $plugins
	 * @return void
	 */
	public function createGruntFile(array $plugins)
	{
		$this->gruntFile->create($plugins);
	}

	/**
	 * Create the package.json file
	 * 
	 * @return void
	 */
	public function createPackageFile()
	{
		$packageFilePath = __DIR__ . "/templates/packagefile.txt";

		// If file exists, copy content into new file called package.json in project root.
		if($this->filesystem->exists($packageFilePath))
		{
			$this->filesystem->put('package.json', $this->filesystem->get($packageFilePath));
		}
	}

	/**
	 * Create an assets folder
	 * 
	 * @param  string $path
	 * @return void
	 */
	public function createAssetsFolder()
	{
		$path = $this->config->get('laravel-grunt::assets_path');

		if( ! $this->filesystem->exists($path))
		{
			$this->filesystem->makeDirectory($path, 0777, true);
		}
	}

	/**
	 * Add /node_modules to .gitignore 
	 * 
	 * @param string $path
	 * @param string $folder
	 */
	public function addToGitingnore($path, $folder)
	{
		$this->filesystem->append($path, $folder);
	}

}
