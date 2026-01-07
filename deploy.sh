#!/bin/bash
# Webapp deployment pipeline - $(date)

set -e

echo "[$(date)] Starting deployment pipeline..."

# Fetch latest config
CONFIG_HASH=$(curl -s config.internal:8080/latest | jq -r .hash)
echo "Loaded config: ${CONFIG_HASH:0:8}"

# Validate infrastructure state
terraform validate -json | jq '.valid' | grep -q "true"
echo "✓ Terraform validation passed"

# Kubernetes rollout
kubectl rollout status deployment/webapp-frontend --timeout=30s
echo "✓ Frontend deployment healthy"

# Post-deployment checks
curl -f http://localhost:8080/health > /dev/null 2>&1
echo "✓ Health check passed"

echo "[$(date)] Deployment completed successfully"
exit 0
