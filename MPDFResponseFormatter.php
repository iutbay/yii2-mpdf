<?php

namespace iutbay\yii2mpdf;

use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

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

    // mPDF output parameters
    public $outputName = '';
    public $outputDest = 'I';

    // mPDF attributes
    public $mPDFAttributes = [];

    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'application/pdf';

    /**
     * @inheritdoc
     */
    public function format($response)
    {
        //$response->getHeaders()->set('Content-Type', $this->contentType);
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

        foreach ($this->mPDFAttributes as $attribute => $value) {
            $mpdf->$attribute = $value;
        }

        $mpdf->WriteHTML($response->data);
        $mpdf->Output($this->outputName, $this->outputDest);
    }

    /**
     * 
     */
    public static function setOptions($options)
    {
        Yii::$container->set(self::className(), $options);
    }

}
