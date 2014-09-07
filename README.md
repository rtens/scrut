# scrut [![Build Status](https://travis-ci.org/watoki/scrut.png?branch=master)](https://travis-ci.org/watoki/scrut)

Have you ever thought how nice it would be to use dependency injection in your test classes? Search no further!
*scrut* uses [factory] to inject it's properties. Just extend from `Specification` (which extends [PHPUnit]'s `TestCase`)
and define its dependencies by marking injectable properties with `<-`.

```php
/**
 * @property DependencyOne one <-
 * @property NotInjected not (because to arrow)
 */
class MyTest extends Specification {

    /** @var DependencyTwo <- **/
    public $two;

    function testSomething() {
        $this->one->createSomeTestContext();
        $this->two->doSomething();
        $this->assertTrue($this->one->everythingIsCool());
    }
}

*scrut* can be used to create maintainable test suites which can also serve quite nicely as [living documentation][sbe]
which then can be published (with [dox] for example).

[PHPUnit]: http://phpunit.de
[sbe]: http://specificationbyexample.com/key_ideas.html
[dox]: http://dox.rtens.org