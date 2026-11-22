# AGENTARDS — Agenticus prompt family

Copy a prompt into any AI agent. All prompts inherit `AGENTS.md` (skills, WP standards, mandatory `./bin/verify.sh`).

| File | Use for |
|---|---|
| `AGENTICUS-MAXIMUS.md` | General autonomous work: fixes, a11y, perf, WP.org compliance, versioning/release |
| `AGENTICUS-PURGATORIUS.md` | Evidence-based dead code removal; logs to `CUSTODIAN_LOG.md` |
| `AGENTICUS-OPTIMIZATICUS.md` | Performance campaigns |
| `AGENTICUS-AESTHETICUS.md` | Visual/UX polish |
| `CUSTODIAN_LOG.md` | One row per actual removal or new finding (no no-op rows) |

Rules for all agents: load the relevant WordPress skills first; one change set per PR; no log-only PRs.
