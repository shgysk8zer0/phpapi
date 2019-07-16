<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \StdClass;
use \DateTime;

class Event extends Thing
{
	const TYPE = 'Event';

	protected function _setData(StdClass $data)
	{
		$data->id = intval($data->id);
		$this->setName($data->name);
		$this->setStartDate(new DateTime($data->startDate));

		if (isset($data->endDate)) {
			$this->setEndDate(new DateTime($data->endDate));
		}

		if (isset($data->location)) {
			$this->setLocation(new Place($data->location));
		}

		if (isset($data->description)) {
			$this->setDescription($data->description);
		}

		if (isset($data->organizer)) {
			$this->setOrganizer(new Person($data->organizer));
		}

		if (isset($data->image)) {
			$this->setImage(new ImageObject($data->image));
		}
	}

	public function setLocation(place $place)
	{
		$this->_set('location', $place);
	}

	public function setStartDate(DateTime $dtime)
	{
		$this->_set('startdate', $dtime->format(DateTime::W3C));
	}

	public function setEndDate(DateTime $dtime)
	{
		$this->_set('enddate', $dtime->format(DateTime::W3C));
	}

	public function setOrganizer(Person $organizer)
	{
		$this->_set('organizer', $organizer);
	}
}
