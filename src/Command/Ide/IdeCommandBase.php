<?php

namespace Acquia\Ads\Command\Ide;

use Acquia\Ads\Command\CommandBase;
use AcquiaCloudApi\Endpoints\Ides;
use AcquiaCloudApi\Response\IdeResponse;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Class IdeCommandBase.
 */
abstract class IdeCommandBase extends CommandBase {

  /**
   * @param string $question_text
   * @param \AcquiaCloudApi\Endpoints\Ides $ides_resource
   *
   * @return \AcquiaCloudApi\Response\IdeResponse|null
   */
  protected function promptIdeChoice(
        $question_text,
        Ides $ides_resource
    ): ?IdeResponse {
    $cloud_application_uuid = $this->determineCloudApplication();
    $ides = iterator_to_array($ides_resource->getAll($cloud_application_uuid));
    foreach ($ides as $ide) {
      $ides_list[$ide->uuid] = $ide->label;
    }
    $ide_labels = array_values($ides_list);
    $question = new ChoiceQuestion($question_text, $ide_labels);
    $helper = $this->getHelper('question');
    $choice_id = $helper->ask($this->input, $this->output, $question);
    $ide_uuid = array_search($choice_id, $ides_list, TRUE);
    foreach ($ides as $ide) {
      if ($ide->uuid === $ide_uuid) {
        return $ide;
      }
    }

    return NULL;
  }

}
