# Scrut [![Build Status](https://travis-ci.org/rtens/scrut.png?branch=master)](https://travis-ci.org/rtens/scrut)

*scrut* is a full-fledged, light-weight xUnit-style test framework for PHP.


## The Why ##

Why write another test framekwork for PHP? One could easily argue that there are already [enough], and one would be right.

But apart from the fact, that writing a test framework is great fun, the main reason that lead me to doing so was that
[PhpUnit] started to feel kinda heavy, especially as a requirement of the micro-libraries in my [web application tool kit],
and the alternatives didn't convince me. Other minor reasons where that I needed a more flexible testing framework for
experimenting with test styles and always wanted a framework that is aligned with my work flow.

[enough]: http://en.wikipedia.org/wiki/List_of_unit_testing_frameworks#PHP
[PhpUnit]: http://phpunit.de/
[web application tool kit]: http://github.com/watoki/


## Installation ##

To use *scrut* in your project, require it with [Composer]

    composer require "rtens/scrut"
    
If you would like to develop on *scrut*, clone it with [git], download its dependencies with [Composer] and execute 
the specification with scrut itself (the bootstrapping is the major source of fun when writing test frameworks)

    git clone https://github.com/rtens/mockster.git
    cd mockster
    composer update
    vendor/bin/scrut

[Composer]: http://getcomposer.org/download/
[git]: https://git-scm.com/


## Usage ##

There are three ways to write tests with *scrut* which you can mix as you please.

### Minimalistic ###

The easiest and most minimalistic way to write a test, avoiding all dependencies on scrut, 
is to create a class in a folder (e.g. `spec`) like this:

```php
class Foo {
    function thisOnePasses() {
        assert(true);
    }
    
    function thisOneFails() {
        assert(false, "Bang");
    }
}
```

Note that you don't need to follow any naming convention. *scrut* will execute all public methods of all classes
it finds in the folder you point it to. You can use the `assert` function or throw `Exceptions` to make a test fail.

If you now run `vendor/bin/scrut spec` you should get the following output.

```
.F

---- Failed ----
Foo::thisOneFails [/home/derp/scrut/spec/Foo.php:9]
    Caught E_WARNING from /home/derp/scrut/spec/Foo.php:9
    assert(): Bang failed
    
=( 1 Passed, 1 Failed
```

The dot means that the first test passed, the `F` means that the second one failed and the reason is printed below
followed by a summary of the test run.

### Integrated ###

A more integrated way is let the test class extend `StaticTestSuite` and use the `Assert` class to make assertions.

```php
class Foo extends StaticTestSuite {
    function thisOnePasses() {
        $this->assert("1", 1);
    }
    
    function thisOneFails() {
        $this->assert->equals("1", 2);
    }
    
    function thisOneIsEmpty() {
    }
}
```

The output of `vendor/bin/scrut spec` should now be

```
.FI

---- Incomplete ----
Foo::thisOneIsEmpty [/home/rtens/testScrut/spec/Bar.php:12]
    No assertions made

---- Failed ----
Foo::thisOneFails [/home/rtens/testScrut/spec/Bar.php:9]
    '1' should equal 2

=( 1 Passed, 1 Incomplete, 1 Failed
```

Note that the empty test method results in the test being marked as "incomplete" because no assertions are made.

### Dynamic ###

If you're not a fan of creating classes, then you might like the third way using dynamically created objects.

```php
return (new GenericTestSuite("Foo"))
    ->test("foo", function (Assert $assert) {
        $assert("1", 1);
    });
    ->test("bar", function (Assert $assert) {
        $assert->equals(1+1, 2);
    });
```

which gets you

```
..

=D 2 Passed
```


## Documentation ##

The documentation of *scrut* is written in the form of an executable specification. You find it in the [`spec`] folder.

[`spec`]: http://github.com/rtens/scrut/tree/master/spec/rtens/scrut


## Contribution ##

I'm looking forward to any kind of contribution including feedback about how unnecessary this project is, bugs
and suggestions for missing features. Just open a [new issue] or check out the [open issues].

[new issue]: https://github.com/rtens/mockster/issues/new
[open issues]: https://github.com/rtens/mockster/issues
