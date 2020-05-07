# 1. Support "deserialization" of plain text 

Date: 2020-05-07

## Status

Accepted

## Context

Some APIs, although they declare themselves as REST, return **a plain text string** as response. This breaks the serializer flow as it is expecting, by default, a JSON string. The result is a failure in deserialization and **the processor is interrupted**.

## Decision

We decided to implement a custom deserializer **that accepts plain text**. 

This deserializer is plugged **as a custom deserializer** in JMS' configuration and called whenever the format type "plain_text" is passed to the Serializer class.  

## Metadata
Authors: @andres.rey
People involved: @david.camprubi 
