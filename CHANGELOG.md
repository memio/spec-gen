# CHANGELOG

## 0.3.0: Constructor generation

* constructor generation, same as method except:
    * it inserts constructor at the begining of the class
    * it inserts properties with initialization for each constructor arguments

## 0.2.0: Use statements

* use statement insertion (for each object argument)

## 0.1.1: Fixed object type hints

* fixed Prophecy test doubles type guessing

## 0.1.0: Method Generation

* method generation:
    * it inserts method at the end of the class
    * it typehints object, array and callable arguments
    * it names object arguments after their type
    * it names scalar arguments after a generic name (`argument`)
    * it adds number on names that could collide (e.g. `$argument1, $argument2`)
