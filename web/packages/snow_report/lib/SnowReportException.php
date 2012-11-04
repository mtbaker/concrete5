<?php

class SnowReportException extends exception {

  public function __construct($message = null, $code = 0, Exception $previous = null) {
    // C5 uses a secret static var for their database connection. Because we've used 
    // a separate database, we have to revert to the primary database here. 
    Loader::db(null, null, null, null, true);

    // Write this error out to the log, the c5 global exception handler normally does this,
    // but in this case, we have to do it ourselves because we are catching this and preventing
    // c5 from taking over.
    $log = new Log(LOG_TYPE_EXCEPTIONS, true, true);
    $log->write($this->getFormattedMessage());
    $log->write($this->getTraceAsString());
    $log->close();

    parent::__construct($message, $code, $previous);
  }
  
  private function getFormattedMessage() {
    return sprintf(
        "%s: %s:%d %s (%d)\n",
        t('Snow Report Exception Occurred: '),
        $this->getFile(),
        $this->getLine(),
        $this->getMessage(),
        $this->getCode()
    );
  }
  
  // Send an email only once an hour by using an error.txt file
  // as a timekeeping device. Doesn't work on load balanced servers.
  public function sendThrottledEmail($addresses) {
    $error_file = __DIR__ . '/error.txt';
    if (!file_exists($error_file)) {
      // if the doesn't exist, create it, and we know we haven't had
      // an error before and should send an email
      touch($error_file);
      $this->sendEmail($addresses);
    }
    if ((time() - filemtime($error_file)) > 3600) {
      // if more than an hour since last email, send it again.
      $this->sendEmail($addresses);
      touch($error_file);
    }

    return;
  }

  // Send an email to a CSV list of emails, via the C5 mail helper
  public function sendEmail($addresses) {
    $mh = Loader::helper('mail');
    $mh->setSubject('Snow Report Error');
    $mh->setBody($this->getFormattedMessage());
    foreach (explode(",", $addresses) as $address) {
      $mh->to($address);
    }
    $mh->from('noreply@concrete5.org');
    $mh->sendMail();

    return;
  }
}

/* End of file SnowReportException */
