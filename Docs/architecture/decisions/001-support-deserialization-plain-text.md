# 1. Support "deserialization" of plain text 

Date: 2020-05-07

## Status

Accepted

## Context

External systems can return responses in different formats like JSON, xml, yml, csv and so on. JMS supports the deserialization of some of them, but not all. This means that if the code 
tries to deserialize a response that is not in the expected format, the process is interrupted. One case like this is when the response is defined as **plain text**.

## Decision

We decided to implement a custom deserializer **that accepts plain text**. 

This deserializer is plugged **as a custom deserializer** in JMS' configuration and called whenever the format type "plain_text" is passed to the Serializer class.  

## Metadata
Authors: @andres.rey
People involved: @david.camprubi 
