<?php

use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

class FeatureContext extends MinkContext implements SnippetAcceptingContext {

  /**
   * @Given I am an anonymous user
   */
  public function iAmAnAnonymousUser() {
    // Sine Jekyll doesn't allow login, we just let this pass-through.
  }

  /**
   * @When I select size and add to cart
   */
  public function iSelectSizeAndAddToCart() {
    // Wait for the "S" size to appear
    $xpath = '//*[@id="main"]/div/div[2]/div[2]/div/div[2]/div[1]/ul/li[2]/span[1]';
    $this->waitForXpathNode($xpath);

    // Select the size
    $element = $this->getSession()->getPage()->find('xpath', $xpath);
    $element->click();

    // Add to cart
    $element = $this->getSession()->getPage()->find('css', '.add-to-cart .btn-add');
    $element->click();
  }

  /**
   * @Then I should wait for the text :arg1 to :arg2
   */
  public function iShouldWaitForTheTextTo($text, $appear) {
      $this->waitForXpathNode(".//*[contains(normalize-space(string(text())), \"$text\")]", $appear == 'appear');
  }


  /**
   * @Then /^I wait for css element "([^"]*)" to "([^"]*)"$/
   */
  public function iWaitForCssElement($element, $appear = 'appear') {
    $xpath = $this->getSession()->getSelectorsHandler()->selectorToXpath('css', $element);
    $this->waitForXpathNode($xpath, $appear == 'appear');
  }


  /**
   * Helper function; Execute a function until it return TRUE or timeouts.
   *
   * @param $fn
   *   A callable to invoke.
   * @param int $timeout
   *   The timeout period. Defaults to 10 seconds.
   *
   * @throws Exception
   */
  private function waitFor($fn, $timeout = 5000) {
    $start = microtime(true);
    $end = $start + $timeout / 1000.0;
    while (microtime(true) < $end) {
      if ($fn($this)) {
        return;
      }
    }
    throw new \Exception('waitFor timed out.');
  }
  /**
   * Wait for an element by its XPath to appear or disappear.
   *
   * @param string $xpath
   *   The XPath string.
   * @param bool $appear
   *   Determine if element should appear. Defaults to TRUE.
   *
   * @throws Exception
   */
  private function waitForXpathNode($xpath, $appear = TRUE) {
    $this->waitFor(function($context) use ($xpath, $appear) {
        try {
          $nodes = $context->getSession()->getDriver()->find($xpath);
          if (count($nodes) > 0) {
            $visible = $nodes[0]->isVisible();
            return $appear ? $visible : !$visible;
          }
          return !$appear;
        }
        catch (WebDriver\Exception $e) {
          if ($e->getCode() == WebDriver\Exception::NO_SUCH_ELEMENT) {
            return !$appear;
          }
          throw $e;
        }
      });
  }
}
