# Pedroo Event Logic
Version: 1.0  
Author: Janos & Pedroo Core  
Scope: Defines how Pedroo interprets, manages, evaluates, and automates dog-related events, competitions, and weekly challenges.

---

## 1. Purpose of the Event Logic

Events are the backbone of the Pedigroo ecosystem.  
Pedroo must understand, categorize, evaluate, and react to:

- dog show results  
- sport events  
- working trials  
- virtual competitions  
- weekly photo/video contests  
- breed-specific challenges  

This document defines the rules and logic behind all event handling.

---

## 2. Event Categories

### 2.1 Real-world Events
- FCI dog shows  
- National shows  
- Club shows  
- Working trials  
- Agility, obedience, rally  
- Health screenings (non-diagnostic)

### 2.2 Virtual Events
- Virtual Dog Show  
- Best Video of the Week  
- Cutest Photo of the Week  
- Breed Spotlight Challenge  
- Monthly Theme Contest  

### 2.3 System Events
- Weekly cycle start  
- Weekly cycle end  
- Token distribution  
- Ranking updates  
- Subscription milestones  

---

## 3. Event Structure

Every event follows a unified structure:

- **event_id**  
- **event_type**  
- **category**  
- **breed_filter** (optional)  
- **start_time**  
- **end_time**  
- **submission_rules**  
- **scoring_rules**  
- **result_logic**  
- **reward_logic**  
- **timeline_output**  
- **feed_output**  

Pedroo uses this structure to process any event consistently.

---

## 4. Submission Logic

### 4.1 Real-world Events
- User enters results manually  
- Pedroo validates format  
- Pedroo checks breed consistency  
- Pedroo adds event to timeline  

### 4.2 Virtual Events
- Automatic submission from uploads  
- Pedroo detects:
  - video length  
  - photo quality  
  - breed tags  
  - duplicates  

### 4.3 Weekly Contests
- Submissions open automatically  
- Pedroo collects:
  - likes  
  - views  
  - engagement  
  - breed relevance  

---

## 5. Scoring Logic

### 5.1 Real-world Events
- FCI rules  
- CAC/CACIB logic  
- Class placement  
- Breed-specific titles  
- Pedroo normalizes results

### 5.2 Virtual Events
- Engagement score  
- Quality score  
- Breed relevance score  
- Activity score  
- Anti-cheat filters  

### 5.3 Weekly Contests
- Weighted scoring:
  - 40% engagement  
  - 30% breed relevance  
  - 20% content quality  
  - 10% activity consistency  

Weights are configurable.

---

## 6. Result Logic

Pedroo determines winners by:

- sorting submissions  
- applying tie-breakers  
- validating breed filters  
- checking anti-cheat rules  
- generating final ranking  

Pedroo always stores:
- raw scores  
- normalized scores  
- final ranking  
- reasoning metadata  

---

## 7. Reward Logic

Rewards can include:

- tokens  
- badges  
- ranking points  
- feature unlocks  
- feed highlights  
- timeline highlights  

Pedroo automatically distributes rewards after validation.

---

## 8. Automation Rules

Pedroo autonomously:

### 8.1 Starts Weekly Cycles
- Monday 00:00  
- Creates new contest entries  
- Announces new challenges  

### 8.2 Ends Weekly Cycles
- Sunday 23:59  
- Calculates winners  
- Distributes rewards  
- Updates rankings  
- Publishes results  

### 8.3 Sends Notifications
- Submission reminders  
- Last-hour alerts  
- Winner announcements  
- Token rewards  

### 8.4 Updates Timeline & Feed
- Adds event summary  
- Highlights winners  
- Promotes trending breeds  

---

## 9. Safety & Fairness Rules

Pedroo must ensure:

- no duplicate submissions  
- no spam  
- no artificial engagement  
- no unfair advantage  
- breed fairness  
- transparent scoring  

If anomalies are detected:
- Pedroo flags the event  
- reduces weight  
- or excludes submission  

---

## 10. Extensibility

This event logic supports:

- new virtual competitions  
- seasonal events  
- sponsor events  
- multi-round tournaments  
- kennel-based competitions  
- AI-scored video contests  

All new event types must follow the unified event structure.

---

End of Document.