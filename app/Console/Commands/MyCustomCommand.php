<?php 

namespace App\Console\Commands;

use PDO;
use PDOException;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class MyCustomCommand extends Command
{
    public function readXmlFile($file,$conn,$client_in_function,$siteden_cek)
    {   
        // $file = 'mulakatxmlicerik';
        $filee = 'public/xml/'.$file.'.xml';
        $xmlFilePath = storage_path($filee);
        $xmlFilePath = str_replace("\\", "/",$xmlFilePath);
        $response = NAN;
        if ($siteden_cek){
            $response = $client_in_function->get('https://www.allkaria.com/wp-content/uploads/woo-feed/custom/xml/entegra.xml');
        }
        if (Storage::exists($filee) || $response) {
            if ($siteden_cek){
                $xmlContent = $response->getBody()->getContents();
            }else{
                $xmlContent = Storage::get($xmlFilePath);
            }
            // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            // $out->writeln($xmlContent);
            $xml = new SimpleXMLElement($xmlContent);
            
            // XML verilerini kullanarak işlemleri gerçekleştirin
            $products = $xml->product;

            foreach ($products as $product) {
                $Product_code = $product->Product_code;
                $Product_id = $product->Product_id;
                $Name = $product->Name;
                $mainCategory = $product->mainCategory;
                $CurrencyType = $product->CurrencyType;
                $category = $product->category;
                $category_id = $product->category_id;
                $barcode = $product->barcode;
                $price = $product->price;
                $sale_price = $product->sale_price;
                $tax = $product->tax;
                $Stock = $product->Stock;
                $Brand = $product->Brand;
                $Image = $product->Image;
                $Description = $product->Description;
                $Gtin = $product->Gtin;
                
                $sql = "INSERT INTO ürünler (Product_code, Product_id, Name, mainCategory, CurrencyType, category, category_id, barcode, price, sale_price, tax, Stock, Brand, Image, Description, Gtin)
                        VALUES (:Product_code, :Product_id, :Name, :mainCategory, :CurrencyType, :category, :category_id, :barcode, :price, :sale_price, :tax, :Stock, :Brand, :Image, :Description, :Gtin)";


                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':Product_code', $Product_code);
                $stmt->bindParam(':Product_id', $Product_id);
                $stmt->bindParam(':Name', $Name);
                $stmt->bindParam(':mainCategory', $mainCategory);
                $stmt->bindParam(':CurrencyType', $CurrencyType);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':barcode', $barcode);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':sale_price', $sale_price);
                $stmt->bindParam(':tax', $tax);
                $stmt->bindParam(':Stock', $Stock);
                $stmt->bindParam(':Brand', $Brand);
                $stmt->bindParam(':Image', $Image);
                $stmt->bindParam(':Description', $Description);
                $stmt->bindParam(':Gtin', $Gtin);

                $stmt->execute();

                $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                $out->writeln('Veri başarıyla eklendi.');
            }
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $out->writeln('XML dosyası başarıyla okundu');
        } else {
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $out->writeln('XML dosyası bulunamadı');
        }
    }


    protected $signature = 'xmlekle';
    protected $description = 'My custom command description';

    public function __construct()
    {
        parent::__construct();
    }
    private $server_bagli_degil = True;
    private $conn;
    public function handle()
    {
        $client = new Client();
        while(True){
            if ($this->server_bagli_degil) {
                $servername = "127.0.0.1";
                $username = "root";
                $password = "";
                $database = "laravel";

                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->server_bagli_degil = false;
                    // ekranaBas("MySQL bağlantı başarılı");
                    $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                    $out->writeln("MySQL bağlantı başarılı");
                } catch (PDOException $e) {
                    // ekranaBas("Bağlantı hatası: " . $e->getMessage());
                    $out = new \Symfony\Component\Console\Output\ConsoleOutput();
                    $out->writeln("Bağlantı hatası: " . $e->getMessage());
                }
            } else {
                $file = "mulakatxmlicerik";
                $this->readXmlFile($file,$conn,$client,True); //
                sleep(150);
            }
        }
    }
}