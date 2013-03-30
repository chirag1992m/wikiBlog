<?php
/*  form.php will simplify the task of keeping
	track of errors	and form field values
	that were entered correctly
	its function will be to:
	* keep track of user form filled values
	* keep track of errors in the form
	* printing an error 
*/
?>

<?php
class FormVals
{
	var $values = array();		// holds user submitted form values
	var $errors = array();		// holds submitted form error messages
	var $num_errors;			// holds the number of errors in the submitted forms
	
	/* Class construtor - to get the value and error arrays, used 
		when there is an error with the submitted form and also
		initialize the number of error variable */
	function __construct()
	{
		if(isset($_SESSION['value_array']) && isset($_SESSION['error_array']))
		{
			$this->values = $_SESSION['value_array'];
			$this->errors = $_SESSION['error_array'];
			$this->num_errors = count($this->errors);
			
			unset($_SESSION['value_array']);
			unset($_SESSION['error_array']);
		}
		else
		{
			$this->num_errors = 0;
		}
	}
	
	/* SetValue - records the given form value in the 
		correct field */
	function SetValue($field,$value)
	{
		$this->values[$field] = $value;
	}
	
	/* SetError - records the form error messages with
		the given field */
	function SetError($field,$errmsg)
	{
		$this->errors[$field] = $errmsg;
		$this->num_errors = count($this->errors);
	}
	
	/* GetValue - returns the value attched to a given fieldname.
		if none exists, returns an empty string. */
	function GetValue($field)
	{
		if(array_key_exists($field,$this->values))
			return htmlspecialchars(stripslashes($this->values[$field]));
		else
			return "";
	}
	
	/* GetError - returns the error attached to a given fieldname.
		if none exists, returns an empty string. */
	function GetError($field)
	{
		if(array_key_exists($field,$this->errors))
			return $this->errors[$field];
		else
			return "";
	}
	
	/* GetErrorArray - function to get the whole array of errors */
	function GetErrorArray()
	{
		return $this->errors;
	}
};
?>