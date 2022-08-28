<?php

namespace framework\core;

use framework\flcCommon;

/**
 * FLabsCode
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2022 - 2022, Future Labs Corp-
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    FLabsCode
 * @author    Carlos Arana
 * @copyright    Copyright (c) 2022 - 2022, FLabsCode
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://flabscorpprods.com
 * @since    Version 1.0.0
 * @filesource
 */
class flcLanguage {
    private array $_loaded_langs;
    private array $_lang=[];

    public function load($p_langfile, string $p_idiom = '', string $p_langsuffix = '_lang') : bool {
        static $lang;

        if (empty($p_idiom)) {
            // Use the default , if not defined use english
            $config = &flcCommon::get_config();
            $p_idiom = $config['language'] ?? 'english';
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
            echo $filepath.PHP_EOL;
            if (!file_exists($filepath)) {
                $filepath = BASEPATH.'/core/language/'.$p_idiom.'/'.$p_langfile.$p_langsuffix.'.php';
                echo $filepath.PHP_EOL;

                if (!file_exists($filepath)) {
                    if ($p_idiom != 'english') {
                        if ($this->load($p_langfile, 'english', $p_langsuffix)) {
                            return true;
                        }
                    } else {
                        flcCommon::log_message('error', "flcLanguage:load() - Language file for '$p_langfile' and idiom '$p_idiom or default idiom' doesnt exist");

                    }

                    return false;
                }

                include_once $filepath;

                if (!isset($lang) or !is_array($lang)) {
                    flcCommon::log_message('error', 'flcLanguage:load() - Language file contains no data: language/'.$p_idiom.'/'.$p_langfile);

                    return false;
                }
            }

        }
        $this->_lang = array_merge($this->_lang,$lang);
        unset($lang);
        print_r($this->_lang);

        $this->_loaded_langs[] = $p_langfile.'_'.$p_idiom;
        print_r($this->_loaded_langs);

        return true;

    }

    public function line(string $p_line) : string {
        if (isset($this->_lang[$p_line])) {
            return $this->_lang[$p_line];
        } else {
            flcCommon::log_message('warning', 'flcLanguage:line() - no translation exist for '.$p_line);
            return false;
        }

    }
}