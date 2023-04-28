<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ActiviterRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Eluceo\iCal\Domain\Entity\Calendar;
use ICal\ICal;
use Eluceo\iCal\Component\Event;

use Johngrogg\ICS\ICalEvent;
use Eluceo\iCal\Property\Event\Attendee;
use Eluceo\iCal\Property\Event\Organizer;
use Eluceo\iCal\Property\Event\RecurrenceRule;
use Symfony\Component\Security\Core\Security;


class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(ActiviterRepository $repository,UserRepository $rep): Response
    {
        $user=new User();
        $user=$rep->find(1 );
        $activiters= $repository->findBy(['id_user' => $user]);
        $emploit=[];
        foreach ($activiters as $activ){
            $emploit[]=[
                'id'=>$activ->getId(),
                'title'=> $activ->getTitre(),
                'start'=> $activ->getDateDebut()->format('Y-m-d H:i:s'),
                'end'=> $activ->getDateFin()->format('Y-m-d H:i:s'),
                'backgroundColor'=>$this->getRandomColor()
            ];
        }
        $data=json_encode($emploit);




        return $this->render('calendar/Calendar.html.twig',array("data"=>$data));
    }


    public function generateICalendarFile(ActiviterRepository $repository, UserRepository $rep,Security $security)
    {
        $user=$security->getUser();
        $activiters = $repository->findBy(['id_user' => $user]);

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\n";
        $i=0;
        foreach ($activiters as $activ)
        {
            $i=$i+1;
            $id = $activ->getId();
            $titre = $activ->getTitre();
            $dateDebut = $activ->getDateDebut()->format('Ymd\THis\Z');
            $dateFin = $activ->getDateFin()->format('Ymd\THis\Z');
            $ics .= "Activiter num :".$i."\r\n";
            $ics .= "Date START:" . $dateDebut . "\r\n";
            $ics .= "Date END:" . $dateFin . "\r\n";
            $ics .= "UID:" . $id . "\r\n";
            $ics .= "SUMMARY:" . $titre . "\r\n";
            $ics .= "END:VEVENT\r\n";
        }


        $response = new Response($ics);
        $response->headers->set('bienvenue', 'Calendrier');


        return $response;
    }


    public function downloadCalendar(ActiviterRepository $repository, UserRepository $rep,Security $security)
    {
        $response = $this->generateICalendarFile($repository, $rep,$security);

        return $response;
    }




    private function getRandomColor()
    {
        // Generate random values for the red, green, and blue components
        $r = mt_rand(100, 255);
        $g = mt_rand(100, 255);
        $b = mt_rand(100, 255);

        // Combine the red, green, and blue components into a hexadecimal color string
        $color = "#" . dechex($r) . dechex($g) . dechex($b);

        return $color;
    }




    #[Route('/qr', name: 'app_qr')]
    public function calendarQrCode(ActiviterRepository $repository, UserRepository $rep,Security $security): Response
    {$data=$this->downloadCalendar($repository,$rep,$security);
        $writer = new PngWriter();

// Create QR code
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

// Create generic logo
        $logo = Logo::create(__DIR__.'/assets/symfony.png')
            ->setResizeToWidth(50);

// Create generic label
        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);

// Validate the result
        $writer->validateResult($result, $data);
        // Directly output the QR code
        header('Content-Type: '.$result->getMimeType());
        echo $result->getString();

// Save it to a file
        $result->saveToFile(__DIR__.'/qrcode.png');

// Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $dataUri = $result->getDataUri();
        return $dataUri;
    }








}
