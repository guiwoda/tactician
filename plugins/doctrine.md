---
layout: default
permalink: /plugins/doctrine-orm/
title: Doctrine ORM
---

# Doctrine

[![Author](http://img.shields.io/badge/author-@rosstuck-blue.svg?style=flat-square)](https://twitter.com/rosstuck)
[![Source](http://img.shields.io/badge/source-league/tactician--doctrine-blue.svg?style=flat-square)](https://github.com/thephpleague/tactician-doctrine)
[![Packagist](http://img.shields.io/packagist/v/league/tactician--doctrine.svg?style=flat-square)](https://packagist.org/packages/league/tactician-doctrine)

This package provides a TransactionMiddleware that wraps commands in a Doctrine ORM transaction.

The TransactionMiddleware will start a transaction before each command begins. If the command is successful, it will flush and commit the entity manager. If an exception is raised, TransactionMiddleware rolls back the transaction and rethrows the exception.

Setup is simple:

~~~php
use League\Tactician\CommandBus;
use League\Tactician\Doctrine\ORM\TransactionMiddleware;

$commandBus = new CommandBus(
    [
        new TransactionMiddleware($entityManager)
    ]
);
~~~

## Transactions and Subcommands
Sometimes, you might have a Command that fires off more commands, usually via events. The question then becomes, should these sub-commands run inside the same transaction as the command that originally fired them off?

The recommended approach is having each command run in a separate transaction. This keeps transaction boundaries smaller and makes commands easy to reason about because they only rely on themselves.

You can configure this by using the LockingMiddleware shipped in the core Tactician package. By placing the LockingMiddleware decorator above the TransactionMiddleware, you can ensure that each Command executes separately. For more details, see the [LockingMiddleware documentation](/plugins/locking-middleware/).

Still, Tactician is flexible and you can choose to run all subcommands inside the same transaction if you want. Just place the TransactionMiddleware above the LockingMiddleware or leave the LockingMiddleware off entirely. Again, we don't recommend this but it is possible.