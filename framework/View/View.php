<?php

namespace WAFWork\View;

class View
{
    /**
     * The base path to views
     *
     * @var string
     */
    protected $viewPath;

    /**
     * The base path to compiled views
     *
     * @var string
     */
    protected $cachePath;

    /**
     * Whether to enable view caching
     *
     * @var bool
     */
    protected $cache = false;

    /**
     * Global view data
     *
     * @var array
     */
    protected $shared = [];

    /**
     * Create a new view instance
     *
     * @param string $viewPath
     * @param string $cachePath
     * @param bool $cache
     */
    public function __construct($viewPath, $cachePath, $cache = false)
    {
        $this->viewPath = rtrim($viewPath, '/');
        $this->cachePath = rtrim($cachePath, '/');
        $this->cache = $cache;
    }

    /**
     * Render a view
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render($view, $data = [])
    {
        // Merge with shared data
        $data = array_merge($this->shared, $data);
        
        // Get the view path
        $viewPath = $this->getViewPath($view);
        
        // Check if the view exists
        if (!file_exists($viewPath)) {
            throw new \Exception("View [{$view}] not found.");
        }
        
        // Extract the data to make it accessible in the view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include $viewPath;
        
        // Get the content and end the buffer
        $content = ob_get_clean();
        
        // Process the view for layout and directives
        $content = $this->processView($content, $data);
        
        return $content;
    }

    /**
     * Process the view content
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    protected function processView($content, $data)
    {
        // Process layout extends
        $content = $this->processLayoutExtends($content, $data);
        
        // Process directives
        $content = $this->processDirectives($content);
        
        // Process variables
        $content = $this->processVariables($content);
        
        return $content;
    }

    /**
     * Process layout extends
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    protected function processLayoutExtends($content, $data)
    {
        // Check for extends directive
        if (preg_match('/@extends\(([\'"])(.*?)\1\)/i', $content, $matches)) {
            // Get the layout name
            $layout = $matches[2];
            
            // Remove the extends directive
            $content = str_replace($matches[0], '', $content);
            
            // Extract the sections
            $sections = [];
            preg_match_all('/@section\(([\'"])(.*?)\1\)(.*?)@endsection/is', $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $sections[$match[2]] = $match[3];
                $content = str_replace($match[0], '', $content);
            }
            
            // Add sections to data
            $data['_sections'] = $sections;
            
            // Render the layout
            $content = $this->render($layout, $data);
        }
        
        return $content;
    }

    /**
     * Process directives in the view
     *
     * @param string $content
     * @return string
     */
    protected function processDirectives($content)
    {
        // Process yield directive
        $content = preg_replace_callback('/@yield\(([\'"])(.*?)\1(?:,\s*([\'"])(.*?)\3)?\)/i', function($matches) {
            $section = $matches[2];
            $default = $matches[4] ?? '';
            
            return "<?php echo \$_sections['{$section}'] ?? '{$default}'; ?>";
        }, $content);
        
        // Process if directive
        $content = preg_replace('/@if\((.*?)\)/i', '<?php if ($1): ?>', $content);
        $content = preg_replace('/@elseif\((.*?)\)/i', '<?php elseif ($1): ?>', $content);
        $content = preg_replace('/@else/i', '<?php else: ?>', $content);
        $content = preg_replace('/@endif/i', '<?php endif; ?>', $content);
        
        // Process foreach directive
        $content = preg_replace('/@foreach\((.*?)\)/i', '<?php foreach ($1): ?>', $content);
        $content = preg_replace('/@endforeach/i', '<?php endforeach; ?>', $content);
        
        // Process for directive
        $content = preg_replace('/@for\((.*?)\)/i', '<?php for ($1): ?>', $content);
        $content = preg_replace('/@endfor/i', '<?php endfor; ?>', $content);
        
        // Process while directive
        $content = preg_replace('/@while\((.*?)\)/i', '<?php while ($1): ?>', $content);
        $content = preg_replace('/@endwhile/i', '<?php endwhile; ?>', $content);
        
        // Process include directive
        $content = preg_replace_callback('/@include\(([\'"])(.*?)\1(?:,\s*(.*?))?\)/i', function($matches) {
            $view = $matches[2];
            $data = $matches[3] ?? '[]';
            
            return "<?php echo \$this->render('{$view}', {$data}); ?>";
        }, $content);
        
        return $content;
    }

    /**
     * Process variables in the view
     *
     * @param string $content
     * @return string
     */
    protected function processVariables($content)
    {
        // Process variables like {{ $var }}
        $content = preg_replace('/\{\{\s*(.*?)\s*\}\}/i', '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\'); ?>', $content);
        
        // Process unescaped variables like {!! $var !!}
        $content = preg_replace('/\{!!\s*(.*?)\s*!!\}/i', '<?php echo $1; ?>', $content);
        
        return $content;
    }

    /**
     * Get the full path to a view
     *
     * @param string $view
     * @return string
     */
    protected function getViewPath($view)
    {
        // Replace dots with directory separators
        $view = str_replace('.', '/', $view);
        
        // Return the full path
        return $this->viewPath . '/' . $view . '.php';
    }

    /**
     * Share data with all views
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function share($key, $value = null)
    {
        if (is_array($key)) {
            $this->shared = array_merge($this->shared, $key);
        } else {
            $this->shared[$key] = $value;
        }
        
        return $this;
    }
} 