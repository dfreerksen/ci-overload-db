<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_mssql_driver extends CI_DB_mssql_driver {

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	the sql query string
	 * @param	integer	the number of rows to limit the query to
	 * @param	integer	the offset value
	 * @return	string
	 */
	function _limit($sql, $limit, $offset)
	{
		if ( ! $offset)
		{
			$i = $limit;

			return preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 TOP '.$i.' ', $sql);
		}

		if (count($this->ar_orderby) > 0)
		{
			$ordeR_by  = "ORDER BY ";
			$ordeR_by .= implode(', ', $this->ar_orderby);

			if ($this->ar_order !== FALSE)
			{
				$ordeR_by .= ($this->ar_order == 'desc') ? ' DESC' : ' ASC';
			}

			$sql = preg_replace('/(\\'. $ordeR_by .'\n?)/i','', $sql);
			$sql = preg_replace('/(^\SELECT (DISTINCT)?)/i','\\1 row_number() OVER ('.$ordeR_by.') AS rownum, ', $sql);

			return "SELECT * \nFROM (\n" . $sql . ") AS A \nWHERE A.rownum BETWEEN (".($offset + 1).") AND (".($offset + $limit).")";
		}

		else
		{
			echo 'Query must have an order_by clause in order to be offset.';
		}
	}

}