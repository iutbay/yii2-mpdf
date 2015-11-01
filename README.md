# yii2-mpdf

[mPDF](http://www.mpdf1.com/) response formatter for Yii2.

https://packagist.org/packages/iutbay/yii2-mpdf

Installation
------------
The preferred way to install this response formatter is through [composer](http://getcomposer.org/download/).

Either run

```
composer require "iutbay/yii2-mpdf" "*"
```

or add

```json
"iutbay/yii2-mpdf" : "*"
```

to the require section of your application's `composer.json` file.

Configuration
-------------
Add the following lines in the components section of your application configuration :

```php
'response' => [
  'formatters' => [
    'mpdf' => [
      'class' => 'iutbay\yii2mpdf\MPDFResponseFormatter',
      
      // mPDF constructor parameters : http://mpdf1.com/manual/index.php?tid=184
      //'mode' => '',
      //'format' => 'A4',
      //'defaultFontSize' => 0,
      //'defaultFont' => '',
      //'marginLeft' => 15,
      //'marginRight' => 15,
      //'marginTop' => 16,
      //'marginBottom' => 16,
      //'marginHeader' => 9,
      //'marginFooter' => 9,
      //'orientation' => 'P',

      // mPDF options : http://mpdf1.com/manual/index.php?tid=273
      //'mPDFOptions' => [],

      // css file path aliases, e.g. ['@app/web/css/pdf.css']
      //'cssFiles' => [],

      // page header : http://mpdf1.com/manual/index.php?tid=149
      //'header' => null,
      
      // page footer : http://mpdf1.com/manual/index.php?tid=151
      //'footer' => null,

      // ouput options : http://mpdf1.com/manual/index.php?tid=125
      //'outputName' => '',
      //'outputDest' => 'I',
    ],
  ],
],
```

Usage in controllers
--------------------
Example 1 :
```php
public function actionPdf()
{
  Yii::$app->response->format = 'mpdf';
  return $this->render('pdf');
}
```

Example 2 :
```php
public function actionPdf()
{
  Yii::$app->response->format = 'mpdf';
  return [
    'content' => $this->render('pdf'),
    //'options' => [
        // page header : http://mpdf1.com/manual/index.php?tid=149
        //'header' => 'Left|Center|Right',
        // page footer : http://mpdf1.com/manual/index.php?tid=151
        //'footer' => 'Left|Center|{PAGENO}/{nbpg}',
        // ouput options : http://mpdf1.com/manual/index.php?tid=125
        //'outputName' => 'test.pdf',
        //'outputDest' => 'D',
    //],
    // mPDF options : http://mpdf1.com/manual/index.php?tid=273
    //'mPDFOptions' => [],
  ]
}
```
