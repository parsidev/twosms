<?php
namespace Parsidev\MeliPayamak;
ini_set('default_socket_timeout', 5000);
class MeliPayamak
{
    const SEND = "http://api.payamak-panel.com/post/Send.asmx?wsdl";
    const CONTACT = "http://api.payamak-panel.com/post/contacts.asmx?wsdl";
    const RECEIVE = "http://api.payamak-panel.com/post/receive.asmx?wsdl";
    const TICKET = "http://api.payamak-panel.com/post/Tickets.asmx?wsdl";
    const USER = "http://api.payamak-panel.com/post/users.asmx?wsdl";
    const SCHEDULE = "http://api.payamak-panel.com/post/Schedule.asmx?wsdl";
    const REGIONAL = "http://api.payamak-panel.com/post/Actions.asmx?wsdl";

    protected $client;
    protected $username;
    protected $password;
    protected $fromNum;

    public function __construct($username, $password, $fromNumber)
    {
        $this->username = $username;
        $this->password = $password;
        $this->fromNum = $fromNumber;
    }

    private function responseGenerator($response)
    {
        $code = intval($response->string);
        $result = [];
        $result['status'] = $code;
        $result['sent'] = $code > 100;
        if($code > 100)
            $result['deliver'] = $this->getStatuses($code)->int == 1;
        else
           $result['deliver'] = false;
        switch ($result['status']) {
            case 0:
                $result['message'] = "نام کاربری یا رمز عبور اشتباه می‌باشد.";
                break;
            case 1:
                $result['message'] = "درخواست با موفقیت انجام شد.";
                break;
            case 2:
                $result['message'] = "اعتبار، کافی نمی‌باشد.";
                break;
            case 3:
                $result['message'] = "محدودیت در ارسال روزانه";
                break;
            case 4:
                $result['message'] = "محدودیت در حجم ارسال";
                break;
            case 5:
                $result['message'] = "شماره فرستنده معتبر نمی‌باشد.";
                break;
            case 6:
                $result['message'] = "سامانه در حال بروزرسانی می‌باشد.";
                break;
            case 7:
                $result['message'] = "متن حاوی کلمه فیلتر شده می‌باشد.";
                break;
            case 9:
                $result['message'] = "ارسال از خطوط عمومی از طریق وب سرویس امکان‌پذیر نمی‌باشد.";
                break;
            case 10:
                $result['message'] = "کاربر مورد نظر فعال نمی‌باشد.";
                break;
            case 11:
                $result['message'] = "ارسال نشده";
                break;
            case 12:
                $result['message'] = "مدارک کاربر کامل نمی‌باشد.";
                break;
            default:
                $result['message'] = "پیامک با موفقیت ارسال شد.";
                break;
        }
        return $result;
    }


    //Connect Start
    public function connectForSend($timeout)
    {
        $this->client = new Soap(self::SEND, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    public function connectForContact($timeout)
    {
        $this->client = new Soap(self::CONTACT, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    public function connectForReceive($timeout)
    {
        $this->client = new Soap(self::RECEIVE, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    public function connectForTicket($timeout)
    {
        $this->client = new Soap(self::TICKET, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    public function connectForUser($timeout)
    {
        $this->client = new Soap(self::USER, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    public function connectForSchedule($timeout)
    {
        $this->client = new Soap(self::SCHEDULE, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    public function connectForRegional($timeout)
    {
        $this->client = new Soap(self::REGIONAL, ['encoding' => 'UTF-8', 'timeout' => $timeout]);
    }
    //Connect End
    //Send Start
    public function getStatuses($uniqueId, $timeout = 0)
    {
        $this->connectForSend($timeout);
        if (!is_array($uniqueId))
            $uniqueId = [$uniqueId];
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['recIds'] = $uniqueId;
        try{
            $response = $this->client->GetDeliveries($parameters)->GetDeliveriesResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getStatus($uniqueId, $timeout = 0)
    {
        $this->connectForSend($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['recId'] = $uniqueId;
        try{
            $response = $this->client->GetDelivery2($parameters)->GetDeliveryResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function sendSMS($to, $message, $from = null, $type = 'normal', $timeout = 0)
    {
        $this->connectForSend($timeout);
        if (!is_array($to))
            $to = [$to];
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['to'] = $to;
        if (is_null($from))
            $parameters['from'] = $this->fromNum;
        else
            $parameters['from'] = $from;
        $parameters['text'] = $message;
        $parameters['isflash'] = $type == 'flash';
        try{
            $response = $this->client->SendSimpleSMS($parameters)->SendSimpleSMSResult;
            return $this->responseGenerator($response);
        }catch (Exception $exception){
            return false;
        }
    }
    public function getCredit($timeout = 0)
    {
        $this->connectForSend($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        try{
            $response = $this->client->GetCredit($parameters)->GetCreditResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    //Send End
    //Contact Start
    public function addGroup($groupName, $description, $showToChilds, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['groupName'] = $groupName;
        $parameters['Descriptions'] = $description;
        $parameters['showToChilds'] = $showToChilds;
        try{
            $response = $this->client->AddGroup($parameters)->AddGroupResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function addContact($groupId, $firstName, $lastName, $nickName, $corporation, $cellPhone, $phone, $fax,
                               $birthDate, $email, $gender, $province, $city, $address, $postalCode, $additionDate,
                               $additionText, $descriptions, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['groupIds'] = $groupId;
        $parameters['firstname'] = $firstName;
        $parameters['lastname'] = $lastName;
        $parameters['nickname'] = $nickName;
        $parameters['corporation'] = $corporation;
        $parameters['mobilenumber'] = $cellPhone;
        $parameters['phone'] = $phone;
        $parameters['fax'] = $fax;
        $parameters['birthdate'] = $birthDate;
        $parameters['email'] = $email;
        $parameters['gender'] = $gender;
        $parameters['province'] = $province;
        $parameters['city'] = $city;
        $parameters['address'] = $address;
        $parameters['postalCode'] = $postalCode;
        $parameters['additionaldate'] = $additionDate;
        $parameters['additionaltext'] = $additionText;
        $parameters['descriptions'] = $descriptions;
        try{
            $response = $this->client->AddContact($parameters)->AddContactResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function checkMobileExistInContact($mobileNumber, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['mobileNumber'] = $mobileNumber;
        try{
            $response = $this->client->CheckMobileExistInContact($parameters)->CheckMobileExistInContactResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getContacts($groupId, $keyword, $from, $count, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['groupId'] = $groupId;
        $parameters['keyword'] = $keyword;
        $parameters['from'] = $from;
        $parameters['count'] = $count;
        try{
            $response = $this->client->GetContacts($parameters)->GetContactsResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getGroups($timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        try{
            $response = $this->client->GetGroups($parameters)->GetGroupsResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function changeContact($contactId, $mobileNumber, $firstName, $lastName, $nickName, $corporation, $phone,
                                  $fax, $email, $gender, $province, $city, $address, $postalCode, $additionText,
                                  $descriptions, $contactState, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['contactId'] = $contactId;
        $parameters['firstname'] = $firstName;
        $parameters['lastname'] = $lastName;
        $parameters['nickname'] = $nickName;
        $parameters['corporation'] = $corporation;
        $parameters['mobilenumber'] = $mobileNumber;
        $parameters['phone'] = $phone;
        $parameters['fax'] = $fax;
        $parameters['email'] = $email;
        $parameters['gender'] = $gender;
        $parameters['province'] = $province;
        $parameters['city'] = $city;
        $parameters['address'] = $address;
        $parameters['postalCode'] = $postalCode;
        $parameters['additionaltext'] = $additionText;
        $parameters['descriptions'] = $descriptions;
        $parameters['contactStatus'] = $contactState;
        try{
            $response = $this->client->ChangeContact($parameters)->ChangeContactResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function removeContact($mobileNumber, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['mobilenumber'] = $mobileNumber;
        try{
            $response = $this->client->RemoveContact($parameters)->RemoveContactResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getContactEvents($contactId, $timeout = 0)
    {
        $this->connectForContact($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['contactId'] = $contactId;
        try{
            $response = $this->client->GetContactEvents($parameters)->GetContactEventsResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    //Contact End
    //Receive Start
    public function getInboxCount($isRead, $timeout = 0)
    {
        $this->connectForReceive($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['isRead'] = $isRead;
        try{
            $response = $this->client->GetInboxCount($parameters)->GetInboxCountResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getOutBoxCount($timeout = 0)
    {
        $this->connectForReceive($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        try{
            $response = $this->client->GetOutBoxCount($parameters)->GetOutBoxCountResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getMessages($location, $from, $index, $count, $timeout = 0)
    {
        $this->connectForReceive($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['location'] = $location;
        $parameters['from'] = $from;
        $parameters['index'] = $index;
        $parameters['count'] = $count;
        try{
            $response = $this->client->GetMessages($parameters)->GetMessagesResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getMessagesStr($location, $from, $index, $count, $timeout = 0)
    {
        $this->connectForReceive($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['location'] = $location;
        $parameters['from'] = $from;
        $parameters['index'] = $index;
        $parameters['count'] = $count;
        try{
            $response = $this->client->GetMessageStr($parameters)->GetMessageStrResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function getMessageByDate($location, $from, $index, $count, $dateFrom, $dateTo, $timeout = 0)
    {
        $this->connectForReceive($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['location'] = $location;
        $parameters['from'] = $from;
        $parameters['index'] = $index;
        $parameters['count'] = $count;
        $parameters['dateFrom'] = $dateFrom;
        $parameters['dateTo'] = $dateTo;
        try{
            $response = $this->client->GetMessagesByDate($parameters)->GetMessagesByDateResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    public function removeMessage($messageId, $timeout = 0)
    {
        $this->connectForReceive($timeout);
        $parameters['username'] = $this->username;
        $parameters['password'] = $this->password;
        $parameters['msgIds'] = $messageId;
        try{
            $response = $this->client->RemoveMessages($parameters)->RemoveMessagesResult;
            return $response;
        }catch (Exception $exception){
            return false;
        }
    }
    //Receive End
}
