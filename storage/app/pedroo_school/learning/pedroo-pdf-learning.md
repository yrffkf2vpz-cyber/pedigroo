# Pedroo PDF Learning & Schema Mapping

## Overview

Dog pedigree and competition PDFs differ significantly across:
- countries
- kennel clubs
- organizations
- and sometimes even within the same club over time

Because of this, **fully automatic PDF normalization is neither reliable nor ethical**.
Pedroo therefore uses a **human-supervised learning pipeline** to understand and normalize PDF structures.

This document describes how Pedroo learns to read new PDF formats.

---

## Core Principle

> **Pedroo does not guess. Pedroo learns.**

When a PDF cannot be automatically normalized with high confidence, it is routed to the **Pedroo Console**, where a human teaches Pedroo how to interpret it.

---

## The Learning Workflow

### 1. PDF Ingestion

- A PDF is uploaded or detected
- Metadata is extracted (country, club, document type, hash)
- Pedroo attempts automatic structure recognition

If confidence is insufficient, the PDF enters **learning mode**.

---

### 2. Interactive Schema Mapping (Pedroo Console)

In learning mode:

- Each detected column is displayed in a table preview
- Above every column, a **dropdown selector** appears
- The user selects the correct semantic field for each column  
  (e.g. `dog_name`, `registration_number`, `breed`, `date_of_birth`, etc.)

This process is performed by:
- a breed administrator
- a club administrator
- or the system owner

---

### 3. Teaching the Model

Once the mapping is confirmed:

- The column-to-field mapping is stored
- The PDF structure is fingerprinted (layout + hash + metadata)
- Pedroo associates this structure with the learned schema

From this point forward:

> **Pedroo can automatically process all PDFs of this type.**

---

## Learning Persistence

Pedroo remembers:
- the PDF structure
- the mapping decisions
- who performed the mapping
- when the mapping was created

This ensures:
- auditability
- traceability
- explainability
- ethical AI behavior

---

## Data Flow Layers

The learning pipeline integrates with the broader system architecture:

- **Legacy / Source Layer**  
  Raw imported data and historical formats

- **Pedroo Learning Layer (`pedroo_*`)**  
  Normalized but non-public data  
  Includes learned mappings and intermediate representations

- **Pending Layer (`pending_*`)**  
  Data awaiting validation or approval

- **Public Domain Layer (`pd_*`)**  
  Final, structured, trusted data

---

## Why This Matters

This approach allows Pedroo to be:

- country-agnostic
- club-agnostic
- future-proof
- human-supervised
- continuously improving

Most importantly:

> **Pedroo adapts to the world — the world does not have to adapt to Pedroo.**

---

## Design Philosophy

- No silent failures
- No blind automation
- No irreversible guesses

Every learning step is:
- explicit
- reviewable
- reversible
- and teachable

This makes Pedroo a **trustworthy assistant**, not a black box.

---

## Summary

Pedroo’s PDF learning system transforms manual correction into long-term intelligence.

What starts as a single human decision becomes:
- permanent knowledge
- reusable automation
- and shared understanding across the platform
