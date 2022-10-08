<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Inspired in codeigniter , all kudos for his authors
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace framework\core;

use Exception;
use framework\flcCommon;

require_once dirname(__FILE__).'/../flcCommon.php';


/**
 * Language manager class.
 *
 * Load and cache the load lines to be retrieved later, in the language file and
 * idiom required.
 */
class flcLanguage {
    /**
     * Cache of loaded lang files lang/idion.
     *
     * @var array
     */
    private array $_loaded_langs;

    /**
     * Contains all key=>values for all loaded languge/idion files.
     *
     * @var array
     */
    private array $_lang = [];

    // --------------------------------------------------------------------

    /**
     * Load the specified language file for the idiom.
     *
     * @param string|array $p_langfile the language file or array of them, this will be searched in the
     *     BASEPATH/core/language and the APPPATH/language. Only is need the base name no extension or idiom.
     *
     * @param string       $p_idiom the idiom for the language file.
     * @param string       $p_langsuffix by default _lang , bu can be changed by this parameter.
     *
     * @return bool
     * @throws Exception if not main config is loaded.
     */
    public function load($p_langfile, string $p_idiom = '', string $p_langsuffix = '_lang'): bool {
        static $lang;

        if (empty($p_idiom)) {
            // Use the default , if not defined use english
            $langconfig = flcCommon::get_config()->item('language');
            $p_idiom = $langconfig ?? 'english';
        }


        if (is_array($p_langfile)) {
            foreach ($p_langfile as $langfile) {
                // first load if exist in the library , if not we try to load
                // from the application language files.
                if (!$this->load($langfile, $p_idiom, $p_langsuffix)) {
                    return false;
                }
            }

            return true;
        }

        if (!isset($this->_loaded_langs[$p_langfile.'_'.$p_idiom])) {
            // Search first in the application
            $filepath = APPPATH.'language/'.$p_idiom.'/'.$p_langfile.$p_langsuffix.'.php';

            if (!file_exists($filepath)) {
                // search on the framework
                $filepath = BASEPATH.'/core/language/'.$p_idiom.'/'.$p_langfile.$p_langsuffix.'.php';

                if (!file_exists($filepath)) {
                    // f not exist and is no english , try to find in that language.
                    if ($p_idiom != 'english') {
                        if ($this->load($p_langfile, 'english', $p_langsuffix)) {
                            return true;
                        }
                    } else {
                        flcCommon::log_message('error', "flcLanguage->load : Language file for '$p_langfile' and idiom '$p_idiom or default idiom' doesnt exist");
                    }

                    return false;
                }

            }

            include_once $filepath;

            if (!isset($lang) or !is_array($lang)) {
                flcCommon::log_message('error', 'flcLanguage->load : Language file contains no data: language/'.$p_idiom.'/'.$p_langfile);

                return false;
            }

            // add to lang array and loaded langs.
            $this->_lang = array_merge($this->_lang, $lang);
            unset($lang);

            $this->_loaded_langs[$p_langfile.'_'.$p_idiom] = true;
        }


        return true;

    }

    // --------------------------------------------------------------------

    /**
     * @param string $p_line_id the identifier for the specific line to search
     * for the language , if not is loaded return a generic message indicating
     * the translation doesnt exist.
     *
     * @return string translated message string.
     */
    public function line(string $p_line_id): string {
        return $this->_lang[$p_line_id] ?? "No translation for [ $p_line_id ]";

    }
}