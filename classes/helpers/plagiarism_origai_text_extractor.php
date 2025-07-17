<?php
// This file is part of the plagiarism_origai plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace plagiarism_origai\helpers;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../vendor/autoload.php');

class plagiarism_origai_text_extractor
{
    /** @var \stored_file $storedfile */
    protected $storedfile;

    protected $supportedmimetypes = [
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        // 'application/rtf', // .rtf
        // 'application/vnd.oasis.opendocument.text', // .odt
        // 'text/plain', //.txt
        'application/pdf' // .pdf
    ];

    /** @var string $tempfile */
    protected $tempfile;

    public function __construct($storedfile)
    {
        $this->storedfile = $storedfile;
    }

    /**
     * @return bool|string
     */
    public function extract()
    {
        global $CFG;
        $mimetype = $this->storedfile->get_mimetype();

        if(!$this->is_mime_type_supported())
        {
            error_log("Unsupported mimetype: $mimetype");
            return false;
        }
        $this->tempfile = $CFG->tempdir . '/' . $this->storedfile->get_filename();

        try {
            switch($mimetype)
            {
                case 'application/msword':
                    return $this->extract_from_doc();
                    break;
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    return $this->extract_from_docx();
                    break;
                // case 'application/rtf': //TODO: Support .rtf file types
                // case 'application/vnd.oasis.opendocument.text': // //TODO: Support .odt file types

                case 'text/plain':
                    return $this->storedfile->get_content();
                    break;
                case 'application/pdf':
                    return $this->extract_from_pdf();
                    break;
                default:
                    return false;
                    break;
            }
        } catch (\Throwable $th) {
            $filename = $this->storedfile->get_filename();
            error_log("Error: unable to extract text from file($filename) \n exception: ". $th->getMessage());
            return false;
        }
        finally {
            if(file_exists($this->tempfile))
            {
                unlink($this->tempfile);
            }
        }
    }

    /**
     * @return string
     */
    protected function extract_from_pdf()
    {
        $content = $this->storedfile->get_content();
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseContent($content);
        $text = $pdf->getText();
        return $text;
    }

    /**
     * @return bool
     */
    public function is_mime_type_supported()
    {
        $mimetype = $this->storedfile->get_mimetype();
        return in_array($mimetype, $this->supportedmimetypes);
    }

    protected function extract_from_doc()
    {
        $tempfile = $this->tempfile;
        $this->storedfile->copy_content_to($tempfile);
        $fileHandle = fopen($tempfile, "r");
        $line = @fread($fileHandle, filesize($tempfile));
        $lines = explode(chr(0x0D), $line);
        $outtext = "";
        foreach ($lines as $thisline) {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE) || (strlen($thisline) == 0)) {
            } else {
                $outtext .= $thisline . " ";
            }
        }
        $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $outtext);
        unlink($tempfile);
        return ltrim($outtext);
    }

    protected function extract_from_docx()
    {
        $striped_content = '';
        $content = '';
        $tempfile = $this->tempfile;
        $this->storedfile->copy_content_to($tempfile);

        $zip = new \ZipArchive;

        if ($zip->open($tempfile) === true) {
            // Try to locate the main document content
            $index = $zip->locateName('word/document.xml');
            
            if ($index !== false) {
                $content = $zip->getFromIndex($index);
            }

            $zip->close();

            // Clean and format the extracted XML content
            $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
            $content = str_replace('</w:r></w:p>', "\r\n", $content);
            $striped_content = strip_tags($content);

            $content = ltrim($striped_content);
        }
        unlink($tempfile);

        return $content;
    }

    /**
     * @param null $filepath
     * @param null $pathnamehash
     * @return bool|plagiarism_origai_text_extractor
     */
    public static function make($filepath = null, $pathnamehash = null)
    {
        global $DB;
        $filestorage = get_file_storage();
        /** @var \stored_file|false $fileref */
        $fileref = false;
        if($filepath)
        {
            $contenthash = pathinfo($filepath, PATHINFO_FILENAME); // Extracts 'abcdefgh'

            // Get file record
            $filerecord = $DB->get_record('files', ['contenthash' => $contenthash]);
            if ($filerecord) {
                $fs = get_file_storage();
                $fileref = $fs->get_file_instance($filerecord);
            }
        }

        if($pathnamehash)
        {
            $fileref = $filestorage->get_file_by_hash($pathnamehash);
        }
        
        if(!$fileref)
        {
            error_log("File record not found params: " . json_encode(func_get_args()));
            return false;
        }
        return new static($fileref);
    }

    /**
     * @return \stored_file
     */
    public function get_stored_file()
    {
        return $this->storedfile;
    }

}