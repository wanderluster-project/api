## Deterministic Conflic Resolution

Wanderluster uses Entity versioning to merge different snapshots of a Entity together.
Each parameter has a version parameter that increases each time the entity is committed.

If the versions are different, Wanderluster takes the snapshot parameter with the greater version.
However, when merging two snapshots where the parameter version is identical, Wanderluster uses deterministic rules as a tiebreaker.
This gives Wanderluster entities the ability to be distributed and merged in any order (giving it the property that Entities are eventually consistent)

In general for each data type, the tie breaker will take the larger value.

### Boolean Tie-Breaker Rules

Since True > False:

| Snapshot A          | Snapshot B   | Merged Snapshot  |
|---            |---            |---                    |
| TRUE       | FALSE |   TRUE |
| FALSE       | TRUE |   TRUE |

### DateTime Tie-Breaker Rules

The greater of the two DateTime objects wins:

| Snapshot A          | Snapshot B   | Merged Snapshot  |
|---            |---            |---                    |
| 1/1/2020       | 1/1/2020 |   1/2/2020 |
| 1/2/2020       | 1/1/2020 |   1/2/2020 |
 
### Email Tie-Breaker Rules

The greater of the two strings wins (using the > operator)

| Snapshot A          | Snapshot B   | Merged Snapshot  |
|---            |---            |---                    |
| xyz@gmail.com       | abc@gmail.com |   xyz@gmail.com |
| abc@gmail.com       | xyz@gmail.com |   xyz@gmail.com |

### FileSize Tie-Breaker Rules

The greater of the two file sizes wins (using the > operator)

| Snapshot A          | Snapshot B   | Merged Snapshot  |
|---            |---            |---                    |
| 1000       | 2000 |   2000 |
| 2000       | 1000 |   2000 |

### Integer Tie-Breaker Rules

The greater of the two integers wins (using the > operator)

| Snapshot A          | Snapshot B   | Merged Snapshot  |
|---            |---            |---                    |
| 150       | 250 |   250 |
| 250       | 150 |   250 |