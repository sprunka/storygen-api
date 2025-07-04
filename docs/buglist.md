# Known Issues and Bugs

## Prompt Generation Issues

### Problematic Word Combinations
TODO: Fix problematic word combinations in the seed data that can create nonsensical or grammatically incorrect prompts.

#### Adults Age Group
- `clone in` appears as an adjective but is a phrase
- `a past` appears as a noun but is incomplete
- `bartender with a` is an incomplete adjective phrase
- `journalist in` is an incomplete adjective phrase
- `hacker-for` is an incomplete adjective

#### Teens Age Group
- `with secrets` appears as a noun but is a phrase
- `high school` appears in both adjectives and nouns
- `train station at` is an incomplete adjective phrase
- `after-school robotics` as an adjective is too specific
- `group therapy` as an adjective should be in nouns

#### Events Issues
- Some objects in events are actually actions (e.g., "into something magical", "without wings")
- Some objects create grammatically incorrect combinations with verbs
- Incorrect adverb placement (e.g., "stops quickly a comet", "clones quietly a special power")
- Unnatural sentence structures that follow non-English word order
- Redundant or conflicting modifiers (e.g., "fights completely a dream willingly")
- Missing prepositions and conjunctions where needed
- Direct objects placed after adverbs instead of after verbs
- Inconsistent tense usage within events

## Suggested Fixes
1. Review all adjectives to ensure they are single words or proper hyphenated compounds
2. Move phrase-based descriptions to appropriate categories
3. Complete or remove incomplete phrases
4. Standardize naming conventions across age groups
5. Review verb-object combinations for grammatical correctness

## Data Structure Improvements
- Consider adding validation for grammatical compatibility
- Add structure to ensure adjective-noun combinations make sense
- Implement checks for incomplete phrases
