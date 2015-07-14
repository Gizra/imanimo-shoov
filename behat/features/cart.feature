Feature: Blog post
  In order to be able to view a blog post
  As an anonymous user
  We need to be able to have access to a blog post page

  @javascript
  Scenario: Visit blog post page
    Given I am an anonymous user
    When  I visit "claudette-dress#/Black"
    And   I select size and add to cart
    Then  I should wait for the text "Product added to bag" to "appear"
