# LeaderHub Monorepo

LeaderHub is an integrated platform for fitness equipment distributors, combining a public website, an ERP system (LeaderP), and a multi-tenant gym management system (LeadGym) into a single monorepo.

## Structure
- `core/`: Shared models, services, and migrations
- `packages/design-tokens/`: Style dictionary for shared UI tokens
- `apps/website/`: Public e-commerce website
- `apps/hub/`: LeaderP ERP
- `apps/gym/`: LeadGym software
- `storage/`: Shared storage symlinked across apps
