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

    /**
     * mPDF construtor parameters
     * @link http://mpdf1.com/manual/index.php?tid=184
     */
    public $mPDFConstructorOptions = [
        'mode' => '',
        'format' => 'A4',
        'defaultFontSize' => 0,
        'defaultFont' => '',
        'marginLeft' => 15,
        'marginRight' => 15,
        'marginTop' => 16,
        'marginBottom' => 16,
        'marginHeader' => 9,
        'marginFooter' => 9,
        'orientation' => 'P',
    ];

    /**
     * mPDF options
     * @link http://mpdf1.com/manual/index.php?tid=273
     * @var array
     */
    public $mPDFOptions = [];

    /**
     * CSS files
     * @var array
     */
    public $cssFiles = [];

    /**
     * CSS
     * @var string
     */
    public $css;

    /**
     * mPDF header
     * @see \mPDF::SetHeader()
     * @link http://mpdf1.com/manual/index.php?tid=149
     * @var array
     */
    public $header;

    /**
     * mPDF footer
     * @see \mPDF::SetFooter()
     * @link http://mpdf1.com/manual/index.php?tid=151
     * @var array
     */
    public $footer;

    /**
     * mPDF output options
     * @see \mPDF::Output()
     * @link http://mpdf1.com/manual/index.php?tid=125
     */
    public $outputName = '';
    public $outputDest = 'I';

    /**
     * @var \Closure
     */
    public $beforeWrite;

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
    protected function formatMPDF($response)
    {
        $mPDFCO = ArrayHelper::getValue($response->data, 'mPDFConstructorOptions', []);
        $mPDFCO = array_merge($this->mPDFConstructorOptions, $mPDFCO);
        $mpdf = new \mPDF(
            $mPDFCO['mode'],
            $mPDFCO['format'],
            $mPDFCO['defaultFontSize'],
            $mPDFCO['defaultFont'],
            $mPDFCO['marginLeft'],
            $mPDFCO['marginRight'],
            $mPDFCO['marginTop'],
            $mPDFCO['marginBottom'],
            $mPDFCO['marginHeader'],
            $mPDFCO['marginFooter'],
            $mPDFCO['orientation']
        );

        $this->setMPDFOptions($mpdf, $this->mPDFOptions);

        if (is_array($this->cssFiles)) {
            foreach ($this->cssFiles as $file) {
                $css = @file_get_contents(Yii::getAlias($file));
                if (!empty($css))
                    $mpdf->WriteHTML($css, 1);
            }
        }

        if (isset($this->css))
            $mpdf->WriteHTML($this->css, 1);

        $this->setMPDFHeader($mpdf, $this->header);
        $this->setMPDFFooter($mpdf, $this->footer);

        if (is_string($response->data)) {
            if ($this->beforeWrite !== null)
                call_user_func($this->beforeWrite, $mpdf);
            $mpdf->WriteHTML($response->data, 2);
        } else if (is_array($response->data)) {
            $this->setOptions($mpdf, $response->data['options']);
            $this->setMPDFOptions($mpdf, $response->data['mPDFOptions']);
            if ($this->beforeWrite !== null)
                call_user_func($this->beforeWrite, $mpdf);
            if (isset($response->data['content']))
                $mpdf->WriteHTML($response->data['content'], 2);
        }

        $mpdf->Output($this->outputName, $this->outputDest);
    }

    /**
     * Set formatter options
     */
    protected function setOptions($mpdf, $options)
    {
        if (!is_array($options))
            return;
        
        foreach ($options as $attribute => $value) {
            $this->$attribute = $value;

            if ($attribute === 'header')
                $this->setMPDFHeader($mpdf, $value);

            if ($attribute === 'footer')
                $this->setMPDFFooter($mpdf, $value);
        }
    }

    /**
     * Set mPDF options
     */
    protected function setMPDFOptions($mpdf, $options)
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
    protected function setMPDFHeader($mpdf, $header)
    {
        if (empty($header))
            return;

        $mpdf->SetHeader($header);
    }

    /**
     * Set mPDF footer
     */
    protected function setMPDFFooter($mpdf, $footer)
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
