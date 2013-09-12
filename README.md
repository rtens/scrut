# scrut [![Build Status](https://travis-ci.org/watoki/scrut.png?branch=master)](https://travis-ci.org/watoki/scrut)

A module to support [living documentation][sbe] similar to [cucumber]/[behat] + [relish] by making the test suites of an application browsable.

## Browsing ##

Instead of writing the specification in a plain text file which is parsed to be executed, it is written in code. Thus it can be executed directly without additional libraries and all advantages of modern IDEs like code browsing and refactoring can be used. For browsing and displaying, the specification classes are parsed into a structured format including folders, tags, stories and scenarios. An example:

```PHP
/**
 * @tag currentIteration
 * @property GuestFixture guests
 * @property PartyFixture party
 */
class SomeCoolStuffBehavior extends TestCase {

	function story() {
		return "In order to achieve something awesome
				as the guy responsible for awesomeness
				I want to do something awesome."
	}
	
	function scenarioTwoGuests() {
		$this->guests->given_GetsInvitedToThePartyOf('Bart', 'Milhouse');
		$this->guests->given_GetsInvitedToThePartyOf('Lisa', 'Milhouse');
		$this->whenThePartyStarts();
		$this->party->thenThereShouldBe_Guests(2);
	}
	
	[...]
}
```
	
The class name serves as the name of the feature/story/specification and it's description is returned by the `story` method. Scenarios are implemented as methods that start with `scenario` and are executed by the test runner (e.g. [PHPUnit]). The steps are implemented as methods of either fixture classes (`guests` and `party` in this example) or the behavior class itself. When the class is parsed, the post-fix notation of PHP is transformed into an in-fix notation using the underscores in the method names as place holder. The above example would be displayed the following way.

```yaml
Feature: some cool stuff

In order to achieve something awesome
as the guy responsible for awesomeness
I want to do something awesome.

Scenario: two guests
	Given Bart gets invited to the party of Milhouse
	And Lisa gets invited to the party of Milhouse
	When the pary starts
	Then there should be 2 guests
```

## Executing specification ##

Specifications can be executed individually or in groups. If the automation happens just below the presentation layer, its output can be linked to the presentation and the result be displayed for visual inspection. This way, the behaviour of layouts in various scenarios can be checked easily.

## Best practices ##

Scrut is also meant to serve as a framework and document best practices for writing and managing a living documentation.

[sbe]: http://specificationbyexample.com/key_ideas.html
[cucumber]: http://cukes.info/
[behat]: http://behat.org/
[relish]: https://www.relishapp.com/
[PHPUnit]: http://phpunit.de