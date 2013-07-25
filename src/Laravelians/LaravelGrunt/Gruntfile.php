<?php namespace Laravelians\LaravelGrunt;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

class Gruntfile {

	/**
	 * Filesystem Instance
	 *
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * Config Instance
	 *
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Base path to where Gruntfile.js is to be stored
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Option names as stated in the config file
	 * 
	 * @var array
	 */
	protected $options = array('assets_path', 'css_path', 'css_files', 'js_path', 'js_files', 'less_path', 'less_file', 'sass_path', 'sass_file', 'stylus_path', 'sass_file');

	/**
	 * User required plugins
	 * 
	 * @var array
	 */
	protected $plugin;

	/**
	 * Constructor
	 * 
	 * @param Filesystem $filesystem
	 * @param Config       $config
	 * @param string        $path
	 */
	public function __construct(Filesystem $filesystem, Config $config, $path = null)
	{
		$this->filesystem = $filesystem;
		$this->config = $config;
		$this->path = $path ?: base_path();
	}

	/**
	 * Create a custom gruntfile.js base upon users requirements
	 * 
	 * @param  array  $plugins
	 * @return void
	 */
	public function create(array $plugins)
	{
		// Get raw gruntfile.js template (without custom options)
		$rawPath = __DIR__ . '/templates/gruntfile.txt';
		$rawContents = $this->filesystem->get($rawPath);

		// Add user specified options
		$customContent = $this->addOptions($rawContents, $this->options);

		// Generate custom default task
		$customContent = $this->addDefaultTask($customContent, $plugins);

		// Write file
		$this->writeFile($customContent, $this->getPath());
	}

	/**
	 * Create default task line
	 * 
	 * @param string $content
	 * @param array $plugins
	 */
	protected function addDefaultTask($content, $plugins)
	{
		$pattern = "/{{task}}/i";
		$task = "grunt.registerTask('default', ['clean:all', ";

		foreach ($plugins as $plugin)
		{
			$task .= "'" . $plugin ."', ";
		}

		$task .= "'concat', 'uglify', 'cssmin', 'clean:concat']);";

		$content = preg_replace($pattern, $task, $content);

		return $content;
	}

	/**
	 * Add the custom options to gruntfile.js content
	 * 
	 * @param string $content
	 * @param array $plugins
	 */
	protected function addOptions($content, $options)
	{
		foreach ($options as $option)
		{
			$pattern = '/{{' . $option . '}}/i';
			$config = $this->config->get('laravel-grunt::' . $option);
			$content = preg_replace($pattern, $config, $content);
		}
		return $content;
	}

	/**
	 * Write contents to the gruntfile.js
	 * 
	 * @param  string $content
	 * @param  string $path
	 * @return void
	 */
	protected function writeFile($content, $path)
	{
		$this->filesystem->put($path, $content);
	}

	/**
	 * Get the path to the Gruntfile.js
	 *
	 * @return string
	 */
	protected function getPath()
	{
		return $this->path . '/gruntfile.js';
	}

}
