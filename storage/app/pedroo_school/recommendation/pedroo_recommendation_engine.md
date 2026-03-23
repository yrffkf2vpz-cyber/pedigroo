# Pedroo Recommendation Engine
Version: 1.0  
Author: Janos & Pedroo Core  
Scope: Defines how Pedroo recommends content, competitions, actions, and insights to users based on dog data, behavior patterns, and community signals.

---

## 1. Purpose of the Recommendation Engine

The recommendation engine is responsible for delivering personalized, relevant, and timely suggestions to users.  
Its goals:

- increase engagement  
- support dog development  
- highlight community activity  
- promote fairness  
- guide users through the platform  
- automate weekly and monthly cycles  

Pedroo uses this engine to decide **what to show**, **when**, and **to whom**.

---

## 2. Input Data Sources

### 2.1 Dog Profile Data
- breed  
- age  
- health records  
- training history  
- event participation  
- timeline entries  

### 2.2 User Behavior Data
- interaction frequency  
- feature usage  
- content uploads  
- competition participation  
- subscription status  
- token balance  

### 2.3 Community Signals
- likes  
- votes  
- comments  
- shares  
- trending breeds  
- trending content types  

### 2.4 System Events
- weekly cycles  
- monthly cycles  
- new features  
- token economy changes  

---

## 3. Recommendation Categories

Pedroo provides recommendations in several domains:

### 3.1 Content Recommendations
- trending videos  
- popular photos  
- breed-specific posts  
- new user introductions  
- kennel highlights  

### 3.2 Competition Recommendations
- weekly contests  
- virtual dog show categories  
- breed-specific challenges  
- seasonal events  
- sponsor events  

### 3.3 Dog Development Recommendations
- training tips  
- health reminders  
- grooming suggestions  
- activity goals  
- milestone tracking  

### 3.4 Community Recommendations
- dogs to follow  
- kennels to follow  
- groups to join  
- events to attend  

### 3.5 System Recommendations
- subscription renewal  
- token opportunities  
- profile completion  
- missing data alerts  

---

## 4. Recommendation Logic Layers

The engine uses a multi-layered logic system.

### 4.1 Relevance Layer
Pedroo checks:
- breed match  
- age match  
- activity match  
- user interests  
- past behavior  

### 4.2 Engagement Layer
Pedroo evaluates:
- trending content  
- high-performing posts  
- rising creators  
- active breeds  

### 4.3 Personalization Layer
Pedroo adjusts recommendations based on:
- user preferences  
- accepted suggestions  
- ignored suggestions  
- time-of-day patterns  

### 4.4 Safety Layer
Pedroo filters out:
- harmful content  
- spam  
- duplicates  
- irrelevant suggestions  

---

## 5. Scoring Model

Each recommendation receives a score based on weighted factors:

| Factor | Weight | Description |
|-------|--------|-------------|
| Relevance | 40% | Breed, age, interests |
| Engagement | 30% | Likes, views, comments |
| Personalization | 20% | User history |
| Freshness | 10% | New or trending |

Scores are normalized to 0–1.

---

## 6. Recommendation Types & Triggers

### 6.1 Real-time Recommendations
Triggered by:
- new upload  
- new event  
- new follower  
- new milestone  

### 6.2 Scheduled Recommendations
- weekly competition reminders  
- monthly subscription reminders  
- seasonal events  
- token bonus periods  

### 6.3 Passive Recommendations
Triggered when Pedroo detects:
- low engagement  
- missing data  
- incomplete profile  
- inactive periods  

---

## 7. Competition Recommendation Logic

Pedroo recommends competitions based on:

- breed eligibility  
- past participation  
- content type (photo/video)  
- engagement potential  
- weekly cycle timing  

Example:
> “A te fajtádnál most indul a ‘Cutest Photo of the Week’. Feltöltsek egy képet a nevezéshez?”

---

## 8. Dog Development Recommendation Logic

Pedroo analyzes:

- age milestones  
- training gaps  
- health record frequency  
- activity patterns  

Examples:
- “Itt az ideje egy új tréningcél beállításának.”  
- “Hiányzik a legutóbbi oltási adat. Szeretnéd pótolni?”  

---

## 9. Community Recommendation Logic

Pedroo suggests:

- dogs with similar traits  
- kennels with matching breeds  
- trending creators  
- active groups  

Example:
> “Egy új border collie tulajdonos csatlakozott a közösséghez. Szeretnéd követni?”

---

## 10. Subscription & Token Recommendation Logic

### 10.1 Subscription
- expiration reminders  
- renewal offers  
- feature highlights  

### 10.2 Token Economy
- earning opportunities  
- spending suggestions  
- bonus periods  
- reward notifications  

---

## 11. Learning Feedback Loop

Pedroo improves recommendations by tracking:

- accepted suggestions  
- ignored suggestions  
- rejected suggestions  
- engagement after recommendation  

Weights adjust automatically.

---

## 12. Extensibility

This engine supports future modules:

- AI-based video scoring  
- automatic photo classification  
- advanced breed analytics  
- kennel performance insights  
- multi-tier recommendation layers  

All new modules must integrate into the scoring model.

---

End of Document.