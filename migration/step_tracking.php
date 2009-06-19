<?
	function Passed() {
		echo "<script>top.Passed();</script>";
	}

	function Failed($reason) {
		echo "<script>top.Failed('".JsQuote($reason)."');</script>";
	}

	function AddError($text, $isError = 0) {
		echo "<script>top.AddError('".JsQuote($text)."'".($isError ? ",'Error'" : "").");</script>";
	}

?>