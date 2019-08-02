<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \StdClass;
use \DateTime;

class OpeningHoursSpecification extends Thing
{
	public const TYPE = 'OpeningHoursSpecification';

	public function setDayOfWeek(string $day)
	{
		$this->_set('dayOfWeek', $day);
	}

	public function setOpens(string $time)
	{
		$this->_set('opens', $time);
	}

	public function setCloses(string $time)
	{
		$this->_set('closes', $time);
	}

	public function setValidFrom(DateTime $from)
	{
		$this->_set('validFrom', $from->format(DateTime::W3C));
	}

	public function setValidThrough(DateTime $to)
	{
		$this->_set('validThrough', $to->format(DateTime::W3C));
	}

	public static function getFromGroupById(int $id): array
	{
		$stm = static::$_pdo->prepare('SELECT
			`OpeningHoursSpecificationGroup`.`Sunday`,
			`OpeningHoursSpecificationGroup`.`Monday`,
			`OpeningHoursSpecificationGroup`.`Tuesday`,
			`OpeningHoursSpecificationGroup`.`Wednesday`,
			`OpeningHoursSpecificationGroup`.`Thursday`,
			`OpeningHoursSpecificationGroup`.`Friday`,
			`OpeningHoursSpecificationGroup`.`Saturday`
			FROM `Organization`
			JOIN `Place` ON `Place`.`id` = `Organization`.`location`
			JOIN `OpeningHoursSpecificationGroup` ON `OpeningHoursSpecificationGroup`.`id` = `Place`.`openingHoursSpecification`
			WHERE `Organization`.`id` = :id
			LIMIT 1;'
		);
		$stm->execute([':id' => $id]);
		$days = $stm->fetch(\PDO::FETCH_ASSOC);
		$days = array_filter($days);

		if (is_array($days) and ! empty($days)) {
			$days = array_map('intval', array_values($days));
			return OpeningHoursSpecification::getAll(...$days);
		} else {
			return [];
		}
	}

	public static function getFromGroupByUuid(string $uuid): array
	{
		$stm = static::$_pdo->prepare('SELECT
			`OpeningHoursSpecificationGroup`.`Sunday`,
			`OpeningHoursSpecificationGroup`.`Monday`,
			`OpeningHoursSpecificationGroup`.`Tuesday`,
			`OpeningHoursSpecificationGroup`.`Wednesday`,
			`OpeningHoursSpecificationGroup`.`Thursday`,
			`OpeningHoursSpecificationGroup`.`Friday`,
			`OpeningHoursSpecificationGroup`.`Saturday`
			FROM `Organization`
			JOIN `Place` ON `Place`.`id` = `Organization`.`location`
			JOIN `OpeningHoursSpecificationGroup` ON `OpeningHoursSpecificationGroup`.`id` = `Place`.`openingHoursSpecification`
			WHERE `Organization`.`identifier` = :uuid
			LIMIT 1;'
		);

		$stm->execute([':uuid' => $uuid]);
		$days = $stm->fetch(\PDO::FETCH_ASSOC);
		$days = array_filter($days);

		if (is_array($days) and ! empty($days)) {
			$days = array_map('intval', array_values($days));
			return OpeningHoursSpecification::getAll(...$days);
		} else {
			return [];
		}
	}

	public static function getAll(int ...$ids): array
	{
		$sql = sprintf('SELECT `id`,
				`identifier`,
				`dayOfWeek`,
				`opens`,
				`closes`,
				`validFrom`,
				`validThrough`
			FROM `OpeningHoursSpecification`
			WHERE `id` IN (%s)
			LIMIT %d;',
			join(', ', $ids), count($ids)
		);

		$stm = static::$_pdo->prepare($sql);
		$stm->execute();

		return array_map(function(object $day): self
		{
			$result = new self();
			$result->_setData($day);
			return $result;
		}, $stm->fetchAll());
	}

	protected function _setData(object $data)
	{
		$this->_set('identifier', $data->identifier);
		$this->_setId($data->id);
		$this->setDayOfWeek($data->dayOfWeek);
		$this->setOpens($data->opens);
		$this->setCloses($data->closes);

		if (isset($data->validFrom)) {
			$this->setValidFrom(new DateTime($data->validFrom));
		}

		if (isset($data->validThrough)) {
			$this->setValidThrough(new DateTime($data->validThrough));
		}
	}
}
