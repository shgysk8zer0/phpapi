<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait Git
{
	final public function getBranch(): string
	{
		$current = '';
		foreach($this->getBranches() as $branch) {
			if ($branch->current) {
				$current = $branch->name;
				break;
			}
		}
		return $current;
	}

	final public function getBranches(): array
	{
		$branches = explode(PHP_EOL, trim(`git branch`));
		return array_map(function(string $branch_str): \StdClass
		{
			$branch = new \StdClass();
			if ($branch_str[0] === '*') {
				$branch->name = trim(ltrim($branch_str, '*'));
				$branch->current = true;
			} else {
				$branch->name = trim($branch_str);
				$branch->current = false;
			}
			return $branch;
		}, $branches);
	}

	final public function isMaster(): bool
	{
		return $this->getBranch() === 'master';
	}

	final public function pull(): string
	{
		return trim(`git pull`);
	}

	final public function status(): string
	{
		return trim(`git status`);
	}

	final public function updateSubModules(): string
	{
		return trim(`git submodule update --init`);
	}

	final public function fetch(): string
	{
		return trim(`git fetch --all --prune --tags`);
	}

	final public function isClean(): bool
	{
		$status = explode(PHP_EOL, $this->status());
		return $status[count($status) - 1] === 'nothing to commit, working tree clean';
	}
}
