<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use T4\Helper\Path;

class Scss {
    public static function doLoad() {
        $data = [];
        $data['variables'] = Path::getLocalContent('scss/variables.scss');
        $data['custom'] = Path::getLocalContent('scss/custom.scss');

        return $data;
    }

    public static function doSave() {
        $input = Factory::getApplication()->input->post;
        $variables =  $input->getRaw('variables', '');
        $custom =  $input->getRaw('custom', '');

        // save
        Path::saveLocalContent('scss/variables.scss', $variables);
        Path::saveLocalContent('scss/custom.scss', $custom);

        // save template.scss if not exist
        $template = Path::getLocalContent('scss/template.scss');
        if (!trim($template)) {
            $template .= "// Templates Variables.\n@import \"../../scss/vars\";\n\n";
            $template .= "// Local Variables.\n@import \"variables\";\n\n";
            $template .= "// Bootstrap styles.\n@import \"../../scss/bootstrap\";\n\n";
            $template .= "// Template styles.\n@import \"../../scss/all\";\n\n";
            $template .= "// Custom styles.\n@import \"custom\";\n\n";

            Path::saveLocalContent('scss/template.scss', $template);
        }

        // now compile local template css
        require_once T4PATH . '/vendor/autoload.php';
        $scss = new \ScssPhp\ScssPhp\Compiler();
        chdir(T4PATH_LOCAL . '/scss');
        $css = $scss->compileString($template)->getCss();
        Path::saveLocalContent('css/template.css', $css);

        return ['ok' => 1];
    }

    public static function doClean() {
        $file = T4PATH_LOCAL . '/css/template.css';
        if (is_file($file)) {
            if (!File::delete($file)) {
                return ['error' => 'Cannot delete local template.css'];
            }
        }
        return ['ok' => 1];
    }
}
