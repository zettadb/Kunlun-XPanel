<?php


class ExceptionHook
{
	public function SetExceptionHandler()
	{
		set_exception_handler([$this, 'HandleExceptions']);
	}

	public function HandleExceptions($exception)
	{
		$msg = 'Exception of type \'' . get_class($exception) . '\' occurred with Message: ' . $exception->getMessage() . ' in File ' . $exception->getFile() . ' at Line ' . $exception->getLine();

		$msg .= "\r\n Backtrace \r\n";
		$msg .= $exception->getTraceAsString();

		log_message('error', $msg, true);

		$code = $exception->getCode() === 0 ? 500 : $exception->getCode();

		echo json_encode([
			'code' => $code,
			'message' => $exception->getMessage(),
		]);
		exit(0);
	}
}
