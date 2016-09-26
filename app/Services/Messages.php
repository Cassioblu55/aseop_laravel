<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/15/16
 * Time: 6:39 PM
 */

namespace app\Services;


class Messages
{

	const SUCCESS_MESSAGE = 'successMessage';
	const ERROR_MESSAGE = 'errorMessage';

	const DEFAULT_RECORD_UPDATED_MESSAGE = "Record Updated Successfully";
	const DEFAULT_RECORD_ADDED_MESSAGE = "Record Added Successfully";
	const DEFAULT_RECORD_DELETED_MESSAGE = "Record Deleted Successfully";

	const DEFAULT_RECORD_COULD_NOT_BE_ADDED = 'Record could not be added';

	const DEFAULT_RECORD_COULD_NOT_BE_UPDATED = "Record failed to update";

	const SHOW = "show";
	const EDIT = "edit";
	const INDEX = "index";
	const CREATE =  "create";
	const UPLOAD = "upload";

}