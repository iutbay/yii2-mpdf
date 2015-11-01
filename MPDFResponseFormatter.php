<?php

namespace iutbay\yii2mpdf;

use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;
use yii\helpers\ArrayHelper;

/**
 * mPDF response formatter.
 * 
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
class MPDFResponseFormatter extends Component implements ResponseFormatterInterface
{

    // mPDF construtor parameters
    public $mode = '';
    public $format = 'A4';
    public $defaultFontSize = 0;
    public $defaultFont = '';
    public $marginLeft = 15;
    public $marginRight = 15;
    public $marginTop = 16;
    public $marginBottom = 16;
    public $marginHeader = 9;
    public $marginFooter = 9;
    public $orientation = 'P';

    /**
     * mPDF options
     * @var array
     */
    public $mPDFOptions = [];

    // mPDF output parameters
    public $outputName = '';
    public $outputDest = 'I';

    /**
     * CSS
     * @var string
     */
    public $css;

    /**
     * CSS files
     * @var array
     */
    public $cssFiles = [];

    /**
     * mPDF Header, to use with SetHeader()
     * @var array
     */
    public $header;

    /**
     * mPDF Header, to use with SetFooter()
     * @var array
     */
    public $footer;

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        $this->formatMPDF($response);
    }

    /**
     * 
     * @param \yii\web\Response $response
     */
    public function formatMPDF($response)
    {
        $mpdf = new \mPDF(
            $this->mode,
            $this->format,
            $this->defaultFontSize,
            $this->defaultFont,
            $this->marginLeft,
            $this->marginRight,
            $this->marginTop,
            $this->marginBottom,
            $this->marginHeader,
            $this->marginFooter,
            $this->orientation
        );

        $this->setMPDFOptions($mpdf, $this->mPDFOptions);

        if (isset($this->css))
            $mpdf->WriteHTML($this->css, 1);

        if (is_array($this->cssFiles)) {
            foreach ($this->cssFiles as $file) {
                $css = @file_get_contents(Yii::getAlias($file));
                if (!empty($css)) $mpdf->WriteHTML($css, 1);
            }
        }

        if (is_string($response->data)) {
            $this->setMPDFHeader($mpdf, $this->header);
            $this->setMPDFFooter($mpdf, $this->footer);
            $mpdf->WriteHTML($response->data, 2);
        } else if (is_array($response->data)) {
            $this->setOptions($mpdf, $response->data['options']);
            $this->setMPDFOptions($mpdf, $response->data['mPDFOptions']);
            if (isset($response->data['content']))
                $mpdf->WriteHTML($response->data['content'], 2);
        }

        $mpdf->Output($this->outputName, $this->outputDest);
    }
    
    /**
     * Set formatter options
     */
    public function setOptions($mpdf, $options)
    {
        if (!is_array($options))
            return;

        foreach ($options as $attribute => $value) {
            $this->$attribute = $value;

            if ($attribute==='header')
                $this->setMPDFHeader($mpdf, $value);

            if ($attribute==='footer')
                $this->setMPDFFooter($mpdf, $value);
        }
    }

    /**
     * Set mPDF options
     */
    public function setMPDFOptions($mpdf, $options)
    {
        if (!is_array($options))
            return;

        foreach ($options as $attribute => $value) {
            $mpdf->$attribute = $value;
        }
    }

    /**
     * Set mPDF header
     */
    public function setMPDFHeader($mpdf, $header)
    {
        if (empty($header))
            return;
        
        $mpdf->SetHeader($header);
    }

    /**
     * Set mPDF footer
     */
    public function setMPDFFooter($mpdf, $footer)
    {
        if (empty($footer))
            return;
        
        $mpdf->SetFooter($footer);
    }

    /**
     * Set DI container class configuration
     */
    public static function setClassConfiguration($config)
    {
        Yii::$container->set(self::className(), $config);
    }

}
