<?php
class PGsql_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function pgsqlJSON($host){
		$res='{
	"dashboard": {
	"id": null,
	"uid": null,
	"title": "postgresql",
	"tags": [],
	"timezone": "browser",
	"schemaVersion": 16,
	"version": 1,
	"refresh": "5s",
	"templating": {
		"list":[
		{
        "auto": true,
        "auto_count": 200,
        "auto_min": "1s",
        "current": {
          "selected": false,
          "text": "auto",
          "value": "$__auto_interval_interval"
        },
        "hide": 0,
        "label": "Interval",
        "name": "interval",
        "options": [
          {
            "selected": true,
            "text": "auto",
            "value": "$__auto_interval_interval"
          },
          {
            "selected": false,
            "text": "1s",
            "value": "1s"
          },
          {
            "selected": false,
            "text": "5s",
            "value": "5s"
          },
          {
            "selected": false,
            "text": "1m",
            "value": "1m"
          },
          {
            "selected": false,
            "text": "5m",
            "value": "5m"
          },
          {
            "selected": false,
            "text": "1h",
            "value": "1h"
          },
          {
            "selected": false,
            "text": "6h",
            "value": "6h"
          },
          {
            "selected": false,
            "text": "1d",
            "value": "1d"
          }
        ],
        "query": "1s,5s,1m,5m,1h,6h,1d",
        "refresh": 2,
        "skipUrlSync": false,
        "type": "interval"
      },
      {
        "current": {
          "isNone": true,
          "selected": false,
          "text": "None",
          "value": ""
        },
        "datasource": "Prometheus",
        "definition": "label_values(pg_up, project)",
        "hide": 0,
        "includeAll": false,
        "label": "项目",
        "multi": false,
        "name": "project",
        "options": [],
        "query": {
          "query": "label_values(pg_up, project)",
          "refId": "Prometheus-project-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 1,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      },
      {
        "current": {
          "isNone": true,
          "selected": false,
          "text": "None",
          "value": ""
        },
        "datasource": "Prometheus",
        "definition": "label_values(pg_up{project=~\"$project\"}, server)",
        "hide": 0,
        "includeAll": false,
        "label": "主机",
        "multi": false,
        "name": "server",
        "options": [],
        "query": {
          "query": "label_values(pg_up{project=~\"$project\"}, server)",
          "refId": "Prometheus-server-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 1,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      },
      {
        "current": {
          "selected": false,
          "text": "'.$host.'",
          "value": "'.$host.'"
        },
        "datasource": "Prometheus",
        "definition": "label_values(pg_up{project=~\"$project\",server=~\"$server\"}, instance)",
        "hide": 2,
        "includeAll": false,
        "label": "Host",
        "multi": false,
        "name": "host",
        "options": [],
        "query": {
          "query": "label_values(pg_up{project=~\"$project\",server=~\"$server\"}, instance)",
          "refId": "Prometheus-host-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 1,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      },
      {
        "allValue": ".*",
        "current": {
          "selected": false,
          "text": "All",
          "value": "$__all"
        },
        "datasource": "Prometheus",
        "definition": "",
        "hide": 2,
        "includeAll": true,
        "label": "DB",
        "multi": false,
        "name": "db",
        "options": [],
        "query": {
          "query": "label_values(pg_stat_database_tup_fetched{instance=~\"'.$host.'\",datname!~\"template.*|postgres\"},datname)",
          "refId": "Prometheus-db-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 0,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      }
	]
	},
	"links": [
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "http://'.$host.'/metrics",
		  "tooltip": "",
		  "type": "link",
		  "url": "http://'.$host.'/metrics"
		}
    ],
	"panels": [
		{
		  "cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "Reports whether PMM Server can connect to the PostgreSQL instance.",
		  "fieldConfig": {
			"defaults": {
			  "color": {
				"mode": "thresholds"
			  },
			  "mappings": [
				{
				  "options": {
					"0": {
					  "text": "NO"
					},
					"1": {
					  "text": "YES"
					}
				  },
				  "type": "value"
				},
				{
				  "options": {
					"match": "null",
					"result": {
					  "text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
				"mode": "absolute",
				"steps": [
				  {
					"color": "#d44a3a",
					"value": null
				  },
				  {
					"color": "rgba(237, 129, 40, 0.89)",
					"value": 0
				  },
				  {
					"color": "#299c46",
					"value": 1
				  }
				]
			  },
			  "unit": "none"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 2,
			"x": 0,
			"y": 0
		  },
		  "id": 63,
		  "interval": null,
		  "links": [],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "value",
			"graphMode": "area",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
			  "calcs": [
				"mean"
			  ],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
			  "expr": "pg_up{instance=~\"'.$host.'\",job=\"postgresql\"}",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Connected",
		  "type": "stat"
		},
		{
		  "datasource": "Prometheus",
		  "description": "The version of the PostgreSQL instance.",
		  "gridPos": {
			"h": 3,
			"w": 2,
			"x": 2,
			"y": 0
		  },
		  "id": 65,
		  "links": [],
		  "options": {
			"content": "<h1><b><font color=#e68a00><center>$version</center></font></b></h1>",
			"mode": "html"
		  },
		  "pluginVersion": "8.2.1",
		  "title": "Version",
		  "type": "text"
		},
		{
		  "cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "The maximum number of client connections allowed.  Change this value with care as there are some memory resources that are allocated on a per-client basis, so setting max_connections higher will generally increase overall PostgreSQL memory usage.",
		  "fieldConfig": {
			"defaults": {
			  "color": {
				"mode": "thresholds"
			  },
			  "decimals": 0,
			  "mappings": [
				{
				  "options": {
					"match": "null",
					"result": {
					  "text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
				"mode": "absolute",
				"steps": [
				  {
					"color": "green",
					"value": null
				  },
				  {
					"color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "none"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 3,
			"x": 4,
			"y": 0
		  },
		  "id": 86,
		  "interval": null,
		  "links": [
			{
			  "targetBlank": true,
			  "title": "GUC-MAX-CONNECTIONS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-connection.html#GUC-MAX-CONNECTIONS"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
			  "calcs": [
				"lastNotNull"
			  ],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
			  "expr": "max_over_time(pg_settings_max_connections{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_max_connections{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Max Connections",
		  "type": "stat"
		},
		{
		  "cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "Defines the amount of memory the database server uses for shared memory buffers.  Default is 128MB.  Guidance on tuning is 25% of RAM, but generally doesn\'t exceed 40%.",
		  "fieldConfig": {
			"defaults": {
			  "color": {
				"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
				  "options": {
					"match": "null",
					"result": {
					  "text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
				"mode": "absolute",
				"steps": [
				  {
					"color": "green",
					"value": null
				  },
				  {
					"color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 4,
			"x": 7,
			"y": 0
		  },
		  "id": 67,
		  "interval": null,
		  "links": [
			{
			  "targetBlank": true,
			  "title": "GUC-SHARED-BUFFERS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-resource.html#GUC-SHARED-BUFFERS"
			}
	],
	"maxDataPoints": 100,
	"options": {
	"colorMode": "none",
	"graphMode": "none",
	"justifyMode": "auto",
	"orientation": "horizontal",
	"reduceOptions": {
	"calcs": [
	"lastNotNull"
	],
	"fields": "",
	"values": false
	},
	"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_shared_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_shared_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m]) ",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "refId": "A"
			}
		  ],
		  "title": "Shared Buffers",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "The setting wal_buffers defines how much memory is used for caching the write-ahead log entries. Generally this value is small (3% of shared_buffers value), but it may need to be modified for heavily loaded servers.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "green",
					"value": null
				  },
				  {
					  "color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 4,
			"x": 11,
			"y": 0
		  },
		  "id": 68,
		  "interval": null,
		  "links": [
			{
				"title": "GUC-WAL-BUFFERS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-wal.html#GUC-WAL-BUFFERS"
			},
			{
				"targetBlank": true,
			  "title": "GUC-SHARED-BUFFERS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-resource.html#GUC-SHARED-BUFFERS"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_wal_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_wal_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "refId": "A"
			}
		  ],
		  "title": "Disk-Page Buffers",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "The parameter work_mem defines the amount of memory assigned for internal sort operations and hash tables before writing to temporary disk files.  The default is 4MB.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "green",
					"value": null
				  },
				  {
					  "color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 4,
			"x": 15,
			"y": 0
		  },
		  "id": 69,
		  "interval": null,
		  "links": [
			{
				"targetBlank": true,
			  "title": "GUC-WORK-MEM",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-resource.html#GUC-WORK-MEM"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_work_mem_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_work_mem_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Memory Size for each Sort",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "PostgreSQL\'s effective_cache_size variable tunes how much RAM you expect to be available for disk caching.  Generally adding Linux free+cached will give you a good idea.  This value is used by the query planner whether plans will fit in memory, and when defined too low, can lead to some plans rejecting certain indexes.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "green",
					"value": null
				  },
				  {
					  "color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 3,
			"x": 19,
			"y": 0
		  },
		  "id": 70,
		  "interval": null,
		  "links": [
			{
				"targetBlank": true,
			  "title": "GUC-EFFECTIVE-CACHE-SIZE",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-query.html#GUC-EFFECTIVE-CACHE-SIZE"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_effective_cache_size_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_effective_cache_size_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Disk Cache Size",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "Whether autovacuum process is enabled or not.  Generally the solution is to vacuum more often, not less.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "mappings": [
				{
					"options": {
					"0": {
						"text": "NO"
					},
					"1": {
						"text": "YES"
					}
				  },
				  "type": "value"
				},
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "#d44a3a",
					"value": null
				  },
				  {
					  "color": "rgba(237, 129, 40, 0.89)",
					"value": 0
				  },
				  {
					  "color": "#299c46",
					"value": 1
				  }
				]
			  },
			  "unit": "none"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 2,
			"x": 22,
			"y": 0
		  },
		  "id": 85,
		  "interval": null,
		  "links": [
			{
				"targetBlank": true,
			  "title": "AUTOVACUUM",
			  "url": "https://www.postgresql.org/docs/current/static/routine-vacuuming.html#AUTOVACUUM"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "value",
			"graphMode": "area",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_autovacuum{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_autovacuum{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "refId": "A"
			}
		  ],
		  "title": "Autovacuum",
		  "type": "stat"
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 3
		  },
		  "id": 74,
		  "panels": [],
		  "title": "Connections",
		  "type": "row"
		},
		{
			"aliasColors": {
			"Total ": "#bf1b00"
		  },
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 4
		  },
		  "hiddenSeries": false,
		  "id": 23,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideZero": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Total",
			  "color": "#bf1b00",
			  "fill": 0
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(max_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[$interval]) or\nmax_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[5m])) by (state)",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{state}}",
			  "refId": "A"
			},
			{
				"expr": "sum(max_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[$interval]) or\nmax_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[5m]))",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Total",
			  "metric": "pg",
			  "refId": "C",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "PostgreSQL Connections",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {
			"Maximum commections": "#bf1b00"
		  },
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 4
		  },
		  "hiddenSeries": false,
		  "id": 34,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Maximum connections",
			  "color": "#bf1b00",
			  "fill": 0,
			  "stack": false
			}
		  ],
		  "spaceLength": 10,
		  "stack": true,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_database_numbackends{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval]) or \nmax_over_time(pg_stat_database_numbackends{datname=~\"$db\",instance=~\"'.$host.'\"}[5m])",
			  "format": "time_series",
			  "hide": true,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "A"
			},
			{
				"expr": "max_over_time(pg_settings_max_connections{instance=~\"'.$host.'\"}[$interval]) or \nmax_over_time(pg_settings_max_connections{instance=~\"'.$host.'\"}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Maximum connections",
			  "refId": "B"
			},
			{
				"expr": "max_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\",state=\'active\'}[$interval]) or\nmax_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\",state=\'active\'}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "C"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Active Connections",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "none",
			  "label": null,
			  "logBase": 2,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 12
		  },
		  "id": 76,
		  "panels": [],
		  "title": "Tuples",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 13
		  },
		  "hiddenSeries": false,
		  "id": 36,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_tup_fetched{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_tup_fetched{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Fetched",
			  "refId": "A"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_returned{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_returned{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Returned",
			  "refId": "B"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_inserted{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_inserted{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Inserted",
			  "refId": "C"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_updated{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_updated{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Updated",
			  "refId": "D"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_deleted{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_deleted{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Deleted",
			  "refId": "E"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Tuples",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 13
		  },
		  "hiddenSeries": false,
		  "id": 24,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_tup_returned{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_returned{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows returned by queries",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_fetched{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_fetched{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows fetched by queries",
			  "refId": "B",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Read Tuple Activity",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 21
		  },
		  "hiddenSeries": false,
		  "id": 25,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_tup_inserted{instance=~\"'.$host.'\"}[1m])) or\nsum(irate(pg_stat_database_tup_inserted{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows inserted by queries",
			  "metric": "pg_stat_database_tup",
			  "refId": "C",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_updated{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_updated{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows updated by queries",
			  "metric": "pg_stat_database_tup",
			  "refId": "D",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_deleted{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_deleted{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows deleted by queries",
			  "metric": "pg_stat_database_tup",
			  "refId": "E",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Tuples Changed per $interval",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 29
		  },
		  "id": 78,
		  "panels": [],
		  "title": "Transactions",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 30
		  },
		  "hiddenSeries": false,
		  "id": 26,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_xact_commit{instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_xact_commit{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Commits",
			  "metric": "pg_stat_database_xact_commit",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_xact_rollback{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_xact_rollback{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rollbacks",
			  "metric": "pg_stat_database_xact_commit",
			  "refId": "B",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Transactions",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 30
		  },
		  "hiddenSeries": false,
		  "id": 18,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_activity_max_tx_duration{instance='.$host.'}[$interval]) or\nmax_over_time(pg_stat_activity_max_tx_duration{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{state}}",
			  "metric": "pg_stat_activity_max_tx_duration",
			  "refId": "A",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Duration of Transactions",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "s",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "s",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 38
		  },
		  "id": 80,
		  "panels": [],
		  "title": "Temp Files",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "fill": 1,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 39
		  },
		  "hiddenSeries": false,
		  "id": 48,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 1,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": true,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_database_temp_files{instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or \nmax_over_time(pg_stat_database_temp_files{instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "A"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Number of Temp Files",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 1,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 39
		  },
		  "hiddenSeries": false,
		  "id": 49,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 1,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "increase *",
			  "bars": true,
			  "lines": false
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or \nmax_over_time(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "A"
			},
			{
				"expr": "rate(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or\nirate(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "hide": true,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "increase {{datname}}",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Size of Temp Files",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 2,
			  "format": "bytes",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 47
		  },
		  "id": 82,
		  "panels": [],
		  "title": "Conflicts & Locks",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "editable": true,
		  "error": false,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 1,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 48
		  },
		  "hiddenSeries": false,
		  "id": 3,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"alias": "conflicts",
			  "dsType": "prometheus",
			  "expr": "sum(rate(pg_stat_database_deadlocks{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_deadlocks{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Deadlocks",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "A",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "conflicts"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "deadlocks",
			  "dsType": "prometheus",
			  "expr": "sum(rate(pg_stat_database_conflicts{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_conflicts{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Conflicts",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "B",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "deadlocks"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Conflicts/Deadlocks",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": 0,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "editable": true,
		  "error": false,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 48
		  },
		  "hiddenSeries": false,
		  "id": 61,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"alias": "conflicts",
			  "dsType": "prometheus",
			  "expr": "max_over_time(pg_locks_count{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or\nmax_over_time(pg_locks_count{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{mode}} - {{datname}}",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "A",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "conflicts"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Number of Locks",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 56
		  },
		  "id": 84,
		  "panels": [],
		  "title": "Buffers & Blocks Operations",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 57
		  },
		  "hiddenSeries": false,
		  "id": 50,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "/Write */",
			  "yaxis": 2
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "rate(pg_stat_database_blk_read_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[$interval]) or\nirate(pg_stat_database_blk_read_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Read {{datname}}",
			  "refId": "A"
			},
			{
				"expr": "rate(pg_stat_database_blk_write_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[$interval]) or\nirate(pg_stat_database_blk_write_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Write {{datname}}",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Operations with Blocks",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "rps",
			  "label": "",
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"decimals": 0,
			  "format": "wps",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "editable": true,
		  "error": false,
		  "fill": 2,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 57
		  },
		  "hiddenSeries": false,
		  "id": 2,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideZero": false,
			"max": true,
			"min": true,
			"rightSide": false,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_alloc{instance='.$host.'}[$interval]) or \nirate(pg_stat_bgwriter_buffers_alloc{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Allocated",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "A",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_backend_fsync{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_buffers_backend_fsync{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Fsync calls by a backend",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "B",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_backend{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_buffers_backend{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written directly by a backend",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "C",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_clean{instance='.$host.'}[$interval]) or \nirate(pg_stat_bgwriter_buffers_clean{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written by the background writer",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "D",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_checkpoint{instance='.$host.'}[5m]) or\nirate(pg_stat_bgwriter_buffers_checkpoint{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written during checkpoints",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "E",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Buffers",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": 0,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 65
		  },
		  "id": 72,
		  "panels": [],
		  "title": "Others",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "description": "Based on pg_stat_database_conflicts view",
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 66
		  },
		  "hiddenSeries": false,
		  "id": 28,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_bufferpin{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_bufferpin{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Pinned buffers",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_deadlock{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_deadlock{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Deadlocks",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "B",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_lock{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_lock{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Lock timeouts",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "C",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_snapshot{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_snapshot{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Old snapshots",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "D",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_tablespace{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_tablespace{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Dropped tablespaces ",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "E",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Canceled Queries",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "editable": true,
		  "error": false,
		  "fill": 2,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 66
		  },
		  "hiddenSeries": false,
		  "id": 14,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "maxPerRow": 6,
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": true,
		  "pluginVersion": "8.2.1",
		  "pointradius": 1,
		  "points": false,
		  "renderer": "flot",
		  "repeat": null,
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(pg_stat_database_blks_hit{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}) by (datname) /\n(sum(pg_stat_database_blks_hit{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}) by (datname) + \nsum(pg_stat_database_blks_read{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}) by (datname))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Cache hit rate {{datname}}",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Cache Hit Ratio",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "percentunit",
			  "label": null,
			  "logBase": 1,
			  "max": "1",
			  "min": "0",
			  "show": true
			},
			{
				"format": "percent",
			  "label": "",
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 74
		  },
		  "hiddenSeries": false,
		  "id": 22,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideEmpty": false,
			"hideZero": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "rate(pg_stat_bgwriter_checkpoint_sync_time{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_checkpoint_sync_time{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Files Synchronization to disk",
			  "metric": "pg_stat_bgwriter_checkpoint_sync_time",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "rate(pg_stat_bgwriter_checkpoint_write_time{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_checkpoint_write_time{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written Files to disk",
			  "metric": "pg_stat_bgwriter_checkpoint_write_time",
			  "refId": "B",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Checkpoint stats",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "ms",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 82
		  },
		  "id": 60,
		  "panels": [],
		  "title": "Settings",
		  "type": "row"
		},
		{
			"datasource": null,
		  "gridPos": {
			"h": 18,
			"w": 24,
			"x": 0,
			"y": 83
		  },
		  "id": 58,
		  "links": [],
		  "options": {
			"content": "<table style=\"width:100%; border: 2px solid grey;\">\n  <tr><td></td><td></td><td></td><td></td></tr>\n  <tr><td> The maximum number of concurrent connections </td><td>${max_connections}</td><td> Shared memory buffers (bytes) </td><td>${shared_buffers}</td></tr>\n  <tr><td> The maximum memory to be used for query workspaces (bytes)</td><td>${work_mem}</td><td> The number of disk-page buffers in shared memory for WAL (bytes)</td><td>${wal_buffers}</td></tr>\n  <tr><td> The number of pages per write ahead log segment (bytes) </td><td>${wal_segment_size}</td><td>The maximum memory to be used for query workspaces (bytes)</td><td>${maintenance_work_mem}</td></tr>\n  <tr><td> The block size in the write ahead log </td><td>${block_size}</td><td>The maximum distance in log segments between automatic WAL checkpoints</td><td>${checkpoint_segments}</td></tr>\n  <tr><td> Time spent flushing dirty buffers during checkpoint, as fraction of checkpoint interval </td><td>${checkpoint_timeout}</td><td>The maximum number of simultaneously running WAL sender processes</td><td>${max_wal_senders}</td></tr>\n  <tr><td> The minimum size to shrink the WAL to (bytes)</td><td>${min_wal_size}</td><td>The WAL size that triggers a checkpoint (bytes)</td><td>${max_wal_size}</td></tr>\n  <tr><td> Compresses full-page writes written in WAL file </td><td>${wal_compression}</td><td>Maximum number of concurrent worker processes</td><td>${max_worker_processes}</td></tr>\n  <tr><td> The maximum number of parallel processes per executor node </td><td>${max_parallel_workers_per_gather}</td><td>The maximum number of parallel processes per executor node</td><td>${max_parallel_workers}</td></tr>\n  <tr><td> The default statistics target </td><td>${default_statistics_target}</td><td>The planner\'s estimate of the cost of a sequentially fetched disk page </td><td>${seq_page_cost}</td></tr>\n  <tr><td> The planner\'s estimate of the cost of a nonsequentially fetched disk page </td><td>${random_page_cost}</td><td>The planner\'s assumption about the size of the disk cache</td><td>${effective_cache_size}</td></tr>\n  <tr><td> Number of simultaneous requests that can be handled efficiently by the disk subsystem </td><td>${effective_io_concurrency}</td><td>Forces synchronization of updates to disk </td><td>${fsync}</td></tr>\n  <tr><td> Starts the autovacuum subprocess </td><td>${autovacuum}</td><td>Number of tuple inserts, updates, or deletes prior to analyze as a fraction of reltuples</td><td>${autovacuum_analyze_scale_factor}</td></tr>\n  <tr><td> Minimum number of tuple inserts, updates, or deletes prior to analyze </td><td>${autovacuum_analyze_threshold}</td><td>Number of tuple updates or deletes prior to vacuum as a fraction of reltuples</td><td>${autovacuum_vacuum_scale_factor}</td></tr>\n  <tr><td> Minimum number of tuple updates or deletes prior to vacuum </td><td>${autovacuum_vacuum_threshold}</td><td>Vacuum cost amount available before napping, for autovacuum </td><td>${autovacuum_vacuum_cost_limit}</td></tr>\n  <tr><td> Vacuum cost delay in milliseconds, for autovacuum (seconds) </td><td>${autovacuum_vacuum_cost_delay}</td><td> The maximum number of simultaneously running autovacuum worker processes </td><td>${autovacuum_max_workers}</td></tr>\n  <tr><td> Time to sleep between autovacuum runs (seconds) </td><td>${autovacuum_naptime}</td><td>Age at which to autovacuum a table to prevent transaction ID wraparound </td><td>${autovacuum_freeze_max_age}</td></tr>\n  <tr><td> The maximum memory to be used by each autovacuum worker process (bytes)</td><td>${autovacuum_work_mem}</td><td>Multixact age at which to autovacuum a table to prevent multixact wraparound</td><td>${autovacuum_multixact_freeze_max_age}</td></tr>\n  <tr><td> Start a subprocess to capture stderr output and/or csvlogs into log files </td><td>${logging_collector}</td><td>Sets the minimum execution time above which statements will be logged (seconds) </td><td>${log_min_duration_statement}</td></tr>\n  <tr><td> Logs the duration of each completed SQL statement </td><td>${log_duration}</td><td>Logs long lock waits </td><td>${log_lock_waits}</td></tr>\n  <tr><td></td><td></td><td></td><td></td></tr>\n</table>\n\n",
			"mode": "html"
		  },
		  "pluginVersion": "8.2.1",
		  "title": "PostgreSQL Settings",
		  "type": "text"
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 101
		  },
		  "id": 88,
		  "panels": [],
		  "title": "System Summary",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 5,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 102
		  },
		  "hiddenSeries": false,
		  "id": 90,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideEmpty": true,
			"hideZero": true,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 1,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "clamp_max(((avg by (mode) ( (clamp_max(rate(node_cpu_average{instance=~\"'.$host.'\",mode!=\"idle\"}[$interval]),1)) or (clamp_max(irate(node_cpu_average{instance=~\"'.$host.'\",mode!=\"idle\"}[5m]),1)) ))*100 or (avg_over_time(node_cpu_average{instance=~\"'.$host.'\", mode!=\"total\", mode!=\"idle\"}[$interval]) or \navg_over_time(node_cpu_average{instance=~\"'.$host.'\", mode!=\"total\", mode!=\"idle\"}[5m]))),100)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{mode}}",
			  "refId": "A"
			},
			{
				"expr": "",
			  "format": "time_series",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "CPU Usage",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "percent",
			  "label": null,
			  "logBase": 1,
			  "max": "100",
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {
			"Max CPU Core Utilization": "#bf1b00"
		  },
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 102
		  },
		  "hiddenSeries": false,
		  "id": 92,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Max CPU Core Utilization",
			  "lines": false,
			  "pointradius": 1,
			  "points": true,
			  "yaxis": 2
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "(avg_over_time(node_procs_running{instance=~\"'.$host.'\"}[$interval])-1) / scalar(count(node_cpu{mode=\"user\", instance=~\"'.$host.'\"})) or (avg_over_time(node_procs_running{instance=~\"'.$host.'\"}[5m])-1) / scalar(count(node_cpu{mode=\"user\", instance=~\"'.$host.'\"}))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Normalized CPU Load",
			  "refId": "A"
			},
			{
				"expr": "clamp_max(max by () (sum  by (cpu) ( (clamp_max(rate(node_cpu{instance=~\"'.$host.'\",mode!=\"idle\",mode!=\"iowait\"}[$interval]),1)) or (clamp_max(irate(node_cpu{instance=~\"'.$host.'\",mode!=\"idle\",mode!=\"iowait\"}[5m]),1)) )),1)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Max CPU Core Utilization",
			  "refId": "B"
			},
			{
				"expr": "avg_over_time(node_procs_blocked{instance=~\"'.$host.'\"}[$interval]) or avg_over_time(node_procs_blocked{instance=~\"'.$host.'\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "IO Load",
			  "refId": "C"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Saturation and Max Core Usage",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "percentunit",
			  "label": null,
			  "logBase": 1,
			  "max": "1",
			  "min": "0",
			  "show": true
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 110
		  },
		  "hiddenSeries": false,
		  "id": 94,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Disk Writes (Page Out)",
			  "transform": "negative-Y"
			},
			{
				"alias": "Total",
			  "legend": false,
			  "lines": false
			},
			{
				"alias": "Swap Out (Writes)",
			  "transform": "negative-Y"
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "rate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[5m]) * 1024",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Disk Reads (Page In)",
			  "refId": "A"
			},
			{
				"expr": "(rate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[5m]) * 1024)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Disk Writes (Page Out)",
			  "refId": "B"
			},
			{
				"expr": "(rate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[5m]) * 1024 ) + \n(rate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[5m]) * 1024)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Total",
			  "refId": "C"
			},
			{
				"expr": "rate(node_vmstat_pswpin{instance=~\"'.$host.'\"}[$interval]) * 4096 or irate(node_vmstat_pswpin{instance=~\"'.$host.'\"}[5m]) * 4096",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Swap In (Reads)",
			  "refId": "D"
			},
			{
				"expr": "rate(node_vmstat_pswpout{instance=~\"'.$host.'\"}[$interval]) * 4096 or \nirate(node_vmstat_pswpout{instance=~\"'.$host.'\"}[5m]) * 4096",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Swap Out (Writes)",
			  "refId": "E"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Disk I/O and Swap Activity",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "Bps",
			  "label": "Page Out (-) / Page In (+)",
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 110
		  },
		  "hiddenSeries": false,
		  "id": 96,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Outbound",
			  "transform": "negative-Y"
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(node_network_receive_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[$interval])) or \nsum(irate(node_network_receive_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[5m])) or \nsum(max_over_time(rdsosmetrics_network_rx{instance=~\"'.$host.'\"}[$interval])) or \nsum(max_over_time(rdsosmetrics_network_rx{instance=~\"'.$host.'\"}[5m])) ",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Inbound",
			  "refId": "A"
			},
			{
				"expr": "sum(rate(node_network_transmit_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[$interval])) or sum(irate(node_network_transmit_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[5m])) or\nsum(max_over_time(rdsosmetrics_network_tx{instance=~\"'.$host.'\"}[$interval])) or sum(max_over_time(rdsosmetrics_network_tx{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Outbound",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Network Traffic",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "Bps",
			  "label": "Outbound (-) / Inbound (+)",
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		}
	  ]

	},
	"folderId": 0,
	"overwrite": false
}
';
		return $res;
	}

	public function updatePGsqlJSON($host, $id, $uid, $db_title, $db_version)
	{
		$res = '{
"dashboard": {
	"id": '.$id.',
	"uid": "'.$uid.'",
	"title": "'.$db_title.'",
	"version": '.$db_version.',
	"links": [
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "http://'.$host.'/metrics",
		  "tooltip": "",
		  "type": "link",
		  "url": "http://'.$host.'/metrics"
		}
	],
	"panels": [
		{
		  "cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "Reports whether PMM Server can connect to the PostgreSQL instance.",
		  "fieldConfig": {
			"defaults": {
			  "color": {
				"mode": "thresholds"
			  },
			  "mappings": [
				{
				  "options": {
					"0": {
					  "text": "NO"
					},
					"1": {
					  "text": "YES"
					}
				  },
				  "type": "value"
				},
				{
				  "options": {
					"match": "null",
					"result": {
					  "text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
				"mode": "absolute",
				"steps": [
				  {
					"color": "#d44a3a",
					"value": null
				  },
				  {
					"color": "rgba(237, 129, 40, 0.89)",
					"value": 0
				  },
				  {
					"color": "#299c46",
					"value": 1
				  }
				]
			  },
			  "unit": "none"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 2,
			"x": 0,
			"y": 0
		  },
		  "id": 63,
		  "interval": null,
		  "links": [],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "value",
			"graphMode": "area",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
			  "calcs": [
				"mean"
			  ],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
			  "expr": "pg_up{instance=~\"'.$host.'\",job=\"postgresql\"}",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Connected",
		  "type": "stat"
		},
		{
		  "datasource": "Prometheus",
		  "description": "The version of the PostgreSQL instance.",
		  "gridPos": {
			"h": 3,
			"w": 2,
			"x": 2,
			"y": 0
		  },
		  "id": 65,
		  "links": [],
		  "options": {
			"content": "<h1><b><font color=#e68a00><center>$version</center></font></b></h1>",
			"mode": "html"
		  },
		  "pluginVersion": "8.2.1",
		  "title": "Version",
		  "type": "text"
		},
		{
		  "cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "The maximum number of client connections allowed.  Change this value with care as there are some memory resources that are allocated on a per-client basis, so setting max_connections higher will generally increase overall PostgreSQL memory usage.",
		  "fieldConfig": {
			"defaults": {
			  "color": {
				"mode": "thresholds"
			  },
			  "decimals": 0,
			  "mappings": [
				{
				  "options": {
					"match": "null",
					"result": {
					  "text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
				"mode": "absolute",
				"steps": [
				  {
					"color": "green",
					"value": null
				  },
				  {
					"color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "none"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 3,
			"x": 4,
			"y": 0
		  },
		  "id": 86,
		  "interval": null,
		  "links": [
			{
			  "targetBlank": true,
			  "title": "GUC-MAX-CONNECTIONS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-connection.html#GUC-MAX-CONNECTIONS"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
			  "calcs": [
				"lastNotNull"
			  ],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
			  "expr": "max_over_time(pg_settings_max_connections{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_max_connections{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Max Connections",
		  "type": "stat"
		},
		{
		  "cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "Defines the amount of memory the database server uses for shared memory buffers.  Default is 128MB.  Guidance on tuning is 25% of RAM, but generally doesn\'t exceed 40%.",
		  "fieldConfig": {
			"defaults": {
			  "color": {
				"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
				  "options": {
					"match": "null",
					"result": {
					  "text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
				"mode": "absolute",
				"steps": [
				  {
					"color": "green",
					"value": null
				  },
				  {
					"color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 4,
			"x": 7,
			"y": 0
		  },
		  "id": 67,
		  "interval": null,
		  "links": [
			{
			  "targetBlank": true,
			  "title": "GUC-SHARED-BUFFERS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-resource.html#GUC-SHARED-BUFFERS"
			}
	],
	"maxDataPoints": 100,
	"options": {
	"colorMode": "none",
	"graphMode": "none",
	"justifyMode": "auto",
	"orientation": "horizontal",
	"reduceOptions": {
	"calcs": [
	"lastNotNull"
	],
	"fields": "",
	"values": false
	},
	"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_shared_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_shared_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m]) ",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "refId": "A"
			}
		  ],
		  "title": "Shared Buffers",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "The setting wal_buffers defines how much memory is used for caching the write-ahead log entries. Generally this value is small (3% of shared_buffers value), but it may need to be modified for heavily loaded servers.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "green",
					"value": null
				  },
				  {
					  "color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 4,
			"x": 11,
			"y": 0
		  },
		  "id": 68,
		  "interval": null,
		  "links": [
			{
				"title": "GUC-WAL-BUFFERS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-wal.html#GUC-WAL-BUFFERS"
			},
			{
				"targetBlank": true,
			  "title": "GUC-SHARED-BUFFERS",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-resource.html#GUC-SHARED-BUFFERS"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_wal_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_wal_buffers_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "refId": "A"
			}
		  ],
		  "title": "Disk-Page Buffers",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "The parameter work_mem defines the amount of memory assigned for internal sort operations and hash tables before writing to temporary disk files.  The default is 4MB.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "green",
					"value": null
				  },
				  {
					  "color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 4,
			"x": 15,
			"y": 0
		  },
		  "id": 69,
		  "interval": null,
		  "links": [
			{
				"targetBlank": true,
			  "title": "GUC-WORK-MEM",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-resource.html#GUC-WORK-MEM"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_work_mem_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_work_mem_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Memory Size for each Sort",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "PostgreSQL\'s effective_cache_size variable tunes how much RAM you expect to be available for disk caching.  Generally adding Linux free+cached will give you a good idea.  This value is used by the query planner whether plans will fit in memory, and when defined too low, can lead to some plans rejecting certain indexes.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "decimals": 2,
			  "mappings": [
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "green",
					"value": null
				  },
				  {
					  "color": "red",
					"value": 80
				  }
				]
			  },
			  "unit": "bytes"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 3,
			"x": 19,
			"y": 0
		  },
		  "id": 70,
		  "interval": null,
		  "links": [
			{
				"targetBlank": true,
			  "title": "GUC-EFFECTIVE-CACHE-SIZE",
			  "url": "https://www.postgresql.org/docs/current/static/runtime-config-query.html#GUC-EFFECTIVE-CACHE-SIZE"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "none",
			"graphMode": "none",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_effective_cache_size_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_effective_cache_size_bytes{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "A"
			}
		  ],
		  "title": "Disk Cache Size",
		  "type": "stat"
		},
		{
			"cacheTimeout": null,
		  "datasource": "Prometheus",
		  "description": "Whether autovacuum process is enabled or not.  Generally the solution is to vacuum more often, not less.",
		  "fieldConfig": {
			"defaults": {
				"color": {
					"mode": "thresholds"
			  },
			  "mappings": [
				{
					"options": {
					"0": {
						"text": "NO"
					},
					"1": {
						"text": "YES"
					}
				  },
				  "type": "value"
				},
				{
					"options": {
					"match": "null",
					"result": {
						"text": "N/A"
					}
				  },
				  "type": "special"
				}
			  ],
			  "thresholds": {
					"mode": "absolute",
				"steps": [
				  {
					  "color": "#d44a3a",
					"value": null
				  },
				  {
					  "color": "rgba(237, 129, 40, 0.89)",
					"value": 0
				  },
				  {
					  "color": "#299c46",
					"value": 1
				  }
				]
			  },
			  "unit": "none"
			},
			"overrides": []
		  },
		  "gridPos": {
			"h": 3,
			"w": 2,
			"x": 22,
			"y": 0
		  },
		  "id": 85,
		  "interval": null,
		  "links": [
			{
				"targetBlank": true,
			  "title": "AUTOVACUUM",
			  "url": "https://www.postgresql.org/docs/current/static/routine-vacuuming.html#AUTOVACUUM"
			}
		  ],
		  "maxDataPoints": 100,
		  "options": {
			"colorMode": "value",
			"graphMode": "area",
			"justifyMode": "auto",
			"orientation": "horizontal",
			"reduceOptions": {
				"calcs": [
					"lastNotNull"
				],
			  "fields": "",
			  "values": false
			},
			"text": {},
			"textMode": "auto"
		  },
		  "pluginVersion": "8.2.1",
		  "targets": [
			{
				"expr": "max_over_time(pg_settings_autovacuum{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[$interval]) or\nmax_over_time(pg_settings_autovacuum{instance=~\"'.$host.'\",datname=~\"$db\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "refId": "A"
			}
		  ],
		  "title": "Autovacuum",
		  "type": "stat"
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 3
		  },
		  "id": 74,
		  "panels": [],
		  "title": "Connections",
		  "type": "row"
		},
		{
			"aliasColors": {
			"Total ": "#bf1b00"
		  },
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 4
		  },
		  "hiddenSeries": false,
		  "id": 23,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideZero": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Total",
			  "color": "#bf1b00",
			  "fill": 0
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(max_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[$interval]) or\nmax_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[5m])) by (state)",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{state}}",
			  "refId": "A"
			},
			{
				"expr": "sum(max_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[$interval]) or\nmax_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\"}[5m]))",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Total",
			  "metric": "pg",
			  "refId": "C",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "PostgreSQL Connections",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {
			"Maximum commections": "#bf1b00"
		  },
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 4
		  },
		  "hiddenSeries": false,
		  "id": 34,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Maximum connections",
			  "color": "#bf1b00",
			  "fill": 0,
			  "stack": false
			}
		  ],
		  "spaceLength": 10,
		  "stack": true,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_database_numbackends{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval]) or \nmax_over_time(pg_stat_database_numbackends{datname=~\"$db\",instance=~\"'.$host.'\"}[5m])",
			  "format": "time_series",
			  "hide": true,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "A"
			},
			{
				"expr": "max_over_time(pg_settings_max_connections{instance=~\"'.$host.'\"}[$interval]) or \nmax_over_time(pg_settings_max_connections{instance=~\"'.$host.'\"}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Maximum connections",
			  "refId": "B"
			},
			{
				"expr": "max_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\",state=\'active\'}[$interval]) or\nmax_over_time(pg_stat_activity_count{instance=~\"'.$host.'\",datname=~\"$db\",state=\'active\'}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "C"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Active Connections",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "none",
			  "label": null,
			  "logBase": 2,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 12
		  },
		  "id": 76,
		  "panels": [],
		  "title": "Tuples",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 13
		  },
		  "hiddenSeries": false,
		  "id": 36,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_tup_fetched{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_tup_fetched{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Fetched",
			  "refId": "A"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_returned{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_returned{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Returned",
			  "refId": "B"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_inserted{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_inserted{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Inserted",
			  "refId": "C"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_updated{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_updated{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Updated",
			  "refId": "D"
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_deleted{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_deleted{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Deleted",
			  "refId": "E"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Tuples",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 13
		  },
		  "hiddenSeries": false,
		  "id": 24,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_tup_returned{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_returned{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows returned by queries",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_fetched{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_fetched{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows fetched by queries",
			  "refId": "B",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Read Tuple Activity",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 21
		  },
		  "hiddenSeries": false,
		  "id": 25,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_tup_inserted{instance=~\"'.$host.'\"}[1m])) or\nsum(irate(pg_stat_database_tup_inserted{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows inserted by queries",
			  "metric": "pg_stat_database_tup",
			  "refId": "C",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_updated{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_updated{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows updated by queries",
			  "metric": "pg_stat_database_tup",
			  "refId": "D",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_tup_deleted{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_tup_deleted{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rows deleted by queries",
			  "metric": "pg_stat_database_tup",
			  "refId": "E",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Tuples Changed per $interval",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 29
		  },
		  "id": 78,
		  "panels": [],
		  "title": "Transactions",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 30
		  },
		  "hiddenSeries": false,
		  "id": 26,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_xact_commit{instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_xact_commit{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Commits",
			  "metric": "pg_stat_database_xact_commit",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_xact_rollback{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_xact_rollback{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Rollbacks",
			  "metric": "pg_stat_database_xact_commit",
			  "refId": "B",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Transactions",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 30
		  },
		  "hiddenSeries": false,
		  "id": 18,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_activity_max_tx_duration{instance='.$host.'}[$interval]) or\nmax_over_time(pg_stat_activity_max_tx_duration{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{state}}",
			  "metric": "pg_stat_activity_max_tx_duration",
			  "refId": "A",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Duration of Transactions",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "s",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "s",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 38
		  },
		  "id": 80,
		  "panels": [],
		  "title": "Temp Files",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "fill": 1,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 39
		  },
		  "hiddenSeries": false,
		  "id": 48,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 1,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": true,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_database_temp_files{instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or \nmax_over_time(pg_stat_database_temp_files{instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "A"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Number of Temp Files",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 1,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 39
		  },
		  "hiddenSeries": false,
		  "id": 49,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 1,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "increase *",
			  "bars": true,
			  "lines": false
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "max_over_time(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or \nmax_over_time(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{datname}}",
			  "refId": "A"
			},
			{
				"expr": "rate(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or\nirate(pg_stat_database_temp_bytes{instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "hide": true,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "increase {{datname}}",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Size of Temp Files",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 2,
			  "format": "bytes",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 47
		  },
		  "id": 82,
		  "panels": [],
		  "title": "Conflicts & Locks",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "editable": true,
		  "error": false,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 1,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 48
		  },
		  "hiddenSeries": false,
		  "id": 3,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"alias": "conflicts",
			  "dsType": "prometheus",
			  "expr": "sum(rate(pg_stat_database_deadlocks{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_deadlocks{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Deadlocks",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "A",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "conflicts"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "deadlocks",
			  "dsType": "prometheus",
			  "expr": "sum(rate(pg_stat_database_conflicts{datname=~\"$db\",instance=~\"'.$host.'\"}[$interval])) or \nsum(irate(pg_stat_database_conflicts{datname=~\"$db\",instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Conflicts",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "B",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "deadlocks"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Conflicts/Deadlocks",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": 0,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "editable": true,
		  "error": false,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 48
		  },
		  "hiddenSeries": false,
		  "id": 61,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"alias": "conflicts",
			  "dsType": "prometheus",
			  "expr": "max_over_time(pg_locks_count{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}[$interval]) or\nmax_over_time(pg_locks_count{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{mode}} - {{datname}}",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "A",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "conflicts"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Number of Locks",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 56
		  },
		  "id": 84,
		  "panels": [],
		  "title": "Buffers & Blocks Operations",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 57
		  },
		  "hiddenSeries": false,
		  "id": 50,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "/Write */",
			  "yaxis": 2
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "rate(pg_stat_database_blk_read_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[$interval]) or\nirate(pg_stat_database_blk_read_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Read {{datname}}",
			  "refId": "A"
			},
			{
				"expr": "rate(pg_stat_database_blk_write_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[$interval]) or\nirate(pg_stat_database_blk_write_time{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*|postgres\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Write {{datname}}",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Operations with Blocks",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "rps",
			  "label": "",
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"decimals": 0,
			  "format": "wps",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "editable": true,
		  "error": false,
		  "fill": 2,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 57
		  },
		  "hiddenSeries": false,
		  "id": 2,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideZero": false,
			"max": true,
			"min": true,
			"rightSide": false,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_alloc{instance='.$host.'}[$interval]) or \nirate(pg_stat_bgwriter_buffers_alloc{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Allocated",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "A",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_backend_fsync{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_buffers_backend_fsync{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Fsync calls by a backend",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "B",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_backend{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_buffers_backend{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written directly by a backend",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "C",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_clean{instance='.$host.'}[$interval]) or \nirate(pg_stat_bgwriter_buffers_clean{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written by the background writer",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "D",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			},
			{
				"alias": "Buffers Allocated",
			  "dsType": "prometheus",
			  "expr": "rate(pg_stat_bgwriter_buffers_checkpoint{instance='.$host.'}[5m]) or\nirate(pg_stat_bgwriter_buffers_checkpoint{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "groupBy": [
				{
					"params": [
					"$interval"
				],
				  "type": "time"
				},
				{
					"params": [
					"null"
				],
				  "type": "fill"
				}
			  ],
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written during checkpoints",
			  "measurement": "postgresql",
			  "policy": "default",
			  "refId": "E",
			  "resultFormat": "time_series",
			  "select": [
				[
				  {
					  "params": [
					  "buffers_alloc"
				  ],
					"type": "field"
				  },
				  {
					  "params": [],
					"type": "mean"
				  },
				  {
					  "params": [],
					"type": "difference"
				  }
				]
			  ],
			  "step": 2,
			  "tags": [
				{
					"key": "host",
				  "operator": "=~",
				  "value": "/^'.$host.'$/"
				}
			  ]
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Buffers",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": 0,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 65
		  },
		  "id": 72,
		  "panels": [],
		  "title": "Others",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 0,
		  "description": "Based on pg_stat_database_conflicts view",
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 66
		  },
		  "hiddenSeries": false,
		  "id": 28,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_bufferpin{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_bufferpin{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Pinned buffers",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_deadlock{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_deadlock{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Deadlocks",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "B",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_lock{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_lock{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Lock timeouts",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "C",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_snapshot{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_snapshot{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Old snapshots",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "D",
			  "step": 2
			},
			{
				"expr": "sum(rate(pg_stat_database_conflicts_confl_tablespace{instance=~\"'.$host.'\"}[$interval])) or\nsum(irate(pg_stat_database_conflicts_confl_tablespace{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Dropped tablespaces ",
			  "metric": "pg_stat_database_conflicts_confl_bufferpin",
			  "refId": "E",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Canceled Queries",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"decimals": 0,
			  "format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "editable": true,
		  "error": false,
		  "fill": 2,
		  "fillGradient": 0,
		  "grid": {},
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 66
		  },
		  "hiddenSeries": false,
		  "id": 14,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "maxPerRow": 6,
		  "nullPointMode": "connected",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": true,
		  "pluginVersion": "8.2.1",
		  "pointradius": 1,
		  "points": false,
		  "renderer": "flot",
		  "repeat": null,
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(pg_stat_database_blks_hit{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}) by (datname) /\n(sum(pg_stat_database_blks_hit{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}) by (datname) + \nsum(pg_stat_database_blks_read{datname=~\"$db\",instance=~\"'.$host.'\",datname!~\"template.*\"}) by (datname))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Cache hit rate {{datname}}",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Cache Hit Ratio",
		  "tooltip": {
			"msResolution": false,
			"shared": true,
			"sort": 0,
			"value_type": "cumulative"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "percentunit",
			  "label": null,
			  "logBase": 1,
			  "max": "1",
			  "min": "0",
			  "show": true
			},
			{
				"format": "percent",
			  "label": "",
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 74
		  },
		  "hiddenSeries": false,
		  "id": 22,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideEmpty": false,
			"hideZero": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "rate(pg_stat_bgwriter_checkpoint_sync_time{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_checkpoint_sync_time{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Files Synchronization to disk",
			  "metric": "pg_stat_bgwriter_checkpoint_sync_time",
			  "refId": "A",
			  "step": 2
			},
			{
				"expr": "rate(pg_stat_bgwriter_checkpoint_write_time{instance='.$host.'}[$interval]) or\nirate(pg_stat_bgwriter_checkpoint_write_time{instance='.$host.'}[5m])",
			  "format": "time_series",
			  "hide": false,
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Written Files to disk",
			  "metric": "pg_stat_bgwriter_checkpoint_write_time",
			  "refId": "B",
			  "step": 2
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Checkpoint stats",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "ops",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "ms",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 82
		  },
		  "id": 60,
		  "panels": [],
		  "title": "Settings",
		  "type": "row"
		},
		{
			"datasource": null,
		  "gridPos": {
			"h": 18,
			"w": 24,
			"x": 0,
			"y": 83
		  },
		  "id": 58,
		  "links": [],
		  "options": {
			"content": "<table style=\"width:100%; border: 2px solid grey;\">\n  <tr><td></td><td></td><td></td><td></td></tr>\n  <tr><td> The maximum number of concurrent connections </td><td>${max_connections}</td><td> Shared memory buffers (bytes) </td><td>${shared_buffers}</td></tr>\n  <tr><td> The maximum memory to be used for query workspaces (bytes)</td><td>${work_mem}</td><td> The number of disk-page buffers in shared memory for WAL (bytes)</td><td>${wal_buffers}</td></tr>\n  <tr><td> The number of pages per write ahead log segment (bytes) </td><td>${wal_segment_size}</td><td>The maximum memory to be used for query workspaces (bytes)</td><td>${maintenance_work_mem}</td></tr>\n  <tr><td> The block size in the write ahead log </td><td>${block_size}</td><td>The maximum distance in log segments between automatic WAL checkpoints</td><td>${checkpoint_segments}</td></tr>\n  <tr><td> Time spent flushing dirty buffers during checkpoint, as fraction of checkpoint interval </td><td>${checkpoint_timeout}</td><td>The maximum number of simultaneously running WAL sender processes</td><td>${max_wal_senders}</td></tr>\n  <tr><td> The minimum size to shrink the WAL to (bytes)</td><td>${min_wal_size}</td><td>The WAL size that triggers a checkpoint (bytes)</td><td>${max_wal_size}</td></tr>\n  <tr><td> Compresses full-page writes written in WAL file </td><td>${wal_compression}</td><td>Maximum number of concurrent worker processes</td><td>${max_worker_processes}</td></tr>\n  <tr><td> The maximum number of parallel processes per executor node </td><td>${max_parallel_workers_per_gather}</td><td>The maximum number of parallel processes per executor node</td><td>${max_parallel_workers}</td></tr>\n  <tr><td> The default statistics target </td><td>${default_statistics_target}</td><td>The planner\'s estimate of the cost of a sequentially fetched disk page </td><td>${seq_page_cost}</td></tr>\n  <tr><td> The planner\'s estimate of the cost of a nonsequentially fetched disk page </td><td>${random_page_cost}</td><td>The planner\'s assumption about the size of the disk cache</td><td>${effective_cache_size}</td></tr>\n  <tr><td> Number of simultaneous requests that can be handled efficiently by the disk subsystem </td><td>${effective_io_concurrency}</td><td>Forces synchronization of updates to disk </td><td>${fsync}</td></tr>\n  <tr><td> Starts the autovacuum subprocess </td><td>${autovacuum}</td><td>Number of tuple inserts, updates, or deletes prior to analyze as a fraction of reltuples</td><td>${autovacuum_analyze_scale_factor}</td></tr>\n  <tr><td> Minimum number of tuple inserts, updates, or deletes prior to analyze </td><td>${autovacuum_analyze_threshold}</td><td>Number of tuple updates or deletes prior to vacuum as a fraction of reltuples</td><td>${autovacuum_vacuum_scale_factor}</td></tr>\n  <tr><td> Minimum number of tuple updates or deletes prior to vacuum </td><td>${autovacuum_vacuum_threshold}</td><td>Vacuum cost amount available before napping, for autovacuum </td><td>${autovacuum_vacuum_cost_limit}</td></tr>\n  <tr><td> Vacuum cost delay in milliseconds, for autovacuum (seconds) </td><td>${autovacuum_vacuum_cost_delay}</td><td> The maximum number of simultaneously running autovacuum worker processes </td><td>${autovacuum_max_workers}</td></tr>\n  <tr><td> Time to sleep between autovacuum runs (seconds) </td><td>${autovacuum_naptime}</td><td>Age at which to autovacuum a table to prevent transaction ID wraparound </td><td>${autovacuum_freeze_max_age}</td></tr>\n  <tr><td> The maximum memory to be used by each autovacuum worker process (bytes)</td><td>${autovacuum_work_mem}</td><td>Multixact age at which to autovacuum a table to prevent multixact wraparound</td><td>${autovacuum_multixact_freeze_max_age}</td></tr>\n  <tr><td> Start a subprocess to capture stderr output and/or csvlogs into log files </td><td>${logging_collector}</td><td>Sets the minimum execution time above which statements will be logged (seconds) </td><td>${log_min_duration_statement}</td></tr>\n  <tr><td> Logs the duration of each completed SQL statement </td><td>${log_duration}</td><td>Logs long lock waits </td><td>${log_lock_waits}</td></tr>\n  <tr><td></td><td></td><td></td><td></td></tr>\n</table>\n\n",
			"mode": "html"
		  },
		  "pluginVersion": "8.2.1",
		  "title": "PostgreSQL Settings",
		  "type": "text"
		},
		{
			"collapsed": false,
		  "datasource": "Prometheus",
		  "gridPos": {
			"h": 1,
			"w": 24,
			"x": 0,
			"y": 101
		  },
		  "id": 88,
		  "panels": [],
		  "title": "System Summary",
		  "type": "row"
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 5,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 102
		  },
		  "hiddenSeries": false,
		  "id": 90,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"hideEmpty": true,
			"hideZero": true,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 1,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "clamp_max(((avg by (mode) ( (clamp_max(rate(node_cpu_average{instance=~\"'.$host.'\",mode!=\"idle\"}[$interval]),1)) or (clamp_max(irate(node_cpu_average{instance=~\"'.$host.'\",mode!=\"idle\"}[5m]),1)) ))*100 or (avg_over_time(node_cpu_average{instance=~\"'.$host.'\", mode!=\"total\", mode!=\"idle\"}[$interval]) or \navg_over_time(node_cpu_average{instance=~\"'.$host.'\", mode!=\"total\", mode!=\"idle\"}[5m]))),100)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "{{mode}}",
			  "refId": "A"
			},
			{
				"expr": "",
			  "format": "time_series",
			  "intervalFactor": 1,
			  "legendFormat": "",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "CPU Usage",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "percent",
			  "label": null,
			  "logBase": 1,
			  "max": "100",
			  "min": "0",
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {
			"Max CPU Core Utilization": "#bf1b00"
		  },
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 102
		  },
		  "hiddenSeries": false,
		  "id": 92,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"sort": "avg",
			"sortDesc": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Max CPU Core Utilization",
			  "lines": false,
			  "pointradius": 1,
			  "points": true,
			  "yaxis": 2
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "(avg_over_time(node_procs_running{instance=~\"'.$host.'\"}[$interval])-1) / scalar(count(node_cpu{mode=\"user\", instance=~\"'.$host.'\"})) or (avg_over_time(node_procs_running{instance=~\"'.$host.'\"}[5m])-1) / scalar(count(node_cpu{mode=\"user\", instance=~\"'.$host.'\"}))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Normalized CPU Load",
			  "refId": "A"
			},
			{
				"expr": "clamp_max(max by () (sum  by (cpu) ( (clamp_max(rate(node_cpu{instance=~\"'.$host.'\",mode!=\"idle\",mode!=\"iowait\"}[$interval]),1)) or (clamp_max(irate(node_cpu{instance=~\"'.$host.'\",mode!=\"idle\",mode!=\"iowait\"}[5m]),1)) )),1)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Max CPU Core Utilization",
			  "refId": "B"
			},
			{
				"expr": "avg_over_time(node_procs_blocked{instance=~\"'.$host.'\"}[$interval]) or avg_over_time(node_procs_blocked{instance=~\"'.$host.'\"}[5m])",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "IO Load",
			  "refId": "C"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Saturation and Max Core Usage",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": "0",
			  "show": true
			},
			{
				"format": "percentunit",
			  "label": null,
			  "logBase": 1,
			  "max": "1",
			  "min": "0",
			  "show": true
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 0,
			"y": 110
		  },
		  "hiddenSeries": false,
		  "id": 94,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Disk Writes (Page Out)",
			  "transform": "negative-Y"
			},
			{
				"alias": "Total",
			  "legend": false,
			  "lines": false
			},
			{
				"alias": "Swap Out (Writes)",
			  "transform": "negative-Y"
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "rate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[5m]) * 1024",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Disk Reads (Page In)",
			  "refId": "A"
			},
			{
				"expr": "(rate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[5m]) * 1024)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Disk Writes (Page Out)",
			  "refId": "B"
			},
			{
				"expr": "(rate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgin{instance=~\"'.$host.'\"}[5m]) * 1024 ) + \n(rate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[$interval]) * 1024 or irate(node_vmstat_pgpgout{instance=~\"'.$host.'\"}[5m]) * 1024)",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Total",
			  "refId": "C"
			},
			{
				"expr": "rate(node_vmstat_pswpin{instance=~\"'.$host.'\"}[$interval]) * 4096 or irate(node_vmstat_pswpin{instance=~\"'.$host.'\"}[5m]) * 4096",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Swap In (Reads)",
			  "refId": "D"
			},
			{
				"expr": "rate(node_vmstat_pswpout{instance=~\"'.$host.'\"}[$interval]) * 4096 or \nirate(node_vmstat_pswpout{instance=~\"'.$host.'\"}[5m]) * 4096",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Swap Out (Writes)",
			  "refId": "E"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Disk I/O and Swap Activity",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "Bps",
			  "label": "Page Out (-) / Page In (+)",
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		},
		{
			"aliasColors": {},
		  "bars": false,
		  "dashLength": 10,
		  "dashes": false,
		  "datasource": "Prometheus",
		  "decimals": 2,
		  "fieldConfig": {
			"defaults": {
				"links": []
			},
			"overrides": []
		  },
		  "fill": 2,
		  "fillGradient": 0,
		  "gridPos": {
			"h": 8,
			"w": 12,
			"x": 12,
			"y": 110
		  },
		  "hiddenSeries": false,
		  "id": 96,
		  "legend": {
			"alignAsTable": true,
			"avg": true,
			"current": false,
			"max": true,
			"min": true,
			"show": true,
			"total": false,
			"values": true
		  },
		  "lines": true,
		  "linewidth": 2,
		  "links": [],
		  "nullPointMode": "null",
		  "options": {
			"alertThreshold": true
		  },
		  "percentage": false,
		  "pluginVersion": "8.2.1",
		  "pointradius": 5,
		  "points": false,
		  "renderer": "flot",
		  "seriesOverrides": [
			{
				"alias": "Outbound",
			  "transform": "negative-Y"
			}
		  ],
		  "spaceLength": 10,
		  "stack": false,
		  "steppedLine": false,
		  "targets": [
			{
				"expr": "sum(rate(node_network_receive_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[$interval])) or \nsum(irate(node_network_receive_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[5m])) or \nsum(max_over_time(rdsosmetrics_network_rx{instance=~\"'.$host.'\"}[$interval])) or \nsum(max_over_time(rdsosmetrics_network_rx{instance=~\"'.$host.'\"}[5m])) ",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Inbound",
			  "refId": "A"
			},
			{
				"expr": "sum(rate(node_network_transmit_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[$interval])) or sum(irate(node_network_transmit_bytes{instance=~\"'.$host.'\", device!=\"lo\"}[5m])) or\nsum(max_over_time(rdsosmetrics_network_tx{instance=~\"'.$host.'\"}[$interval])) or sum(max_over_time(rdsosmetrics_network_tx{instance=~\"'.$host.'\"}[5m]))",
			  "format": "time_series",
			  "interval": "$interval",
			  "intervalFactor": 1,
			  "legendFormat": "Outbound",
			  "refId": "B"
			}
		  ],
		  "thresholds": [],
		  "timeFrom": null,
		  "timeRegions": [],
		  "timeShift": null,
		  "title": "Network Traffic",
		  "tooltip": {
			"shared": true,
			"sort": 0,
			"value_type": "individual"
		  },
		  "type": "graph",
		  "xaxis": {
			"buckets": null,
			"mode": "time",
			"name": null,
			"show": true,
			"values": []
		  },
		  "yaxes": [
			{
				"format": "Bps",
			  "label": "Outbound (-) / Inbound (+)",
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": true
			},
			{
				"format": "short",
			  "label": null,
			  "logBase": 1,
			  "max": null,
			  "min": null,
			  "show": false
			}
		  ],
		  "yaxis": {
			"align": false,
			"alignLevel": null
		  }
		}
	],
	"templating": {
		"list":[
	  {
        "auto": true,
        "auto_count": 200,
        "auto_min": "1s",
        "current": {
          "selected": false,
          "text": "auto",
          "value": "$__auto_interval_interval"
        },
        "hide": 0,
        "label": "Interval",
        "name": "interval",
        "options": [
          {
            "selected": true,
            "text": "auto",
            "value": "$__auto_interval_interval"
          },
          {
            "selected": false,
            "text": "1s",
            "value": "1s"
          },
          {
            "selected": false,
            "text": "5s",
            "value": "5s"
          },
          {
            "selected": false,
            "text": "1m",
            "value": "1m"
          },
          {
            "selected": false,
            "text": "5m",
            "value": "5m"
          },
          {
            "selected": false,
            "text": "1h",
            "value": "1h"
          },
          {
            "selected": false,
            "text": "6h",
            "value": "6h"
          },
          {
            "selected": false,
            "text": "1d",
            "value": "1d"
          }
        ],
        "query": "1s,5s,1m,5m,1h,6h,1d",
        "refresh": 2,
        "skipUrlSync": false,
        "type": "interval"
      },
      {
        "current": {
          "isNone": true,
          "selected": false,
          "text": "None",
          "value": ""
        },
        "datasource": "Prometheus",
        "definition": "label_values(pg_up, project)",
        "hide": 0,
        "includeAll": false,
        "label": "项目",
        "multi": false,
        "name": "project",
        "options": [],
        "query": {
          "query": "label_values(pg_up, project)",
          "refId": "Prometheus-project-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 1,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      },
      {
        "current": {
          "isNone": true,
          "selected": false,
          "text": "None",
          "value": ""
        },
        "datasource": "Prometheus",
        "definition": "label_values(pg_up{project=~\"$project\"}, server)",
        "hide": 0,
        "includeAll": false,
        "label": "主机",
        "multi": false,
        "name": "server",
        "options": [],
        "query": {
          "query": "label_values(pg_up{project=~\"$project\"}, server)",
          "refId": "Prometheus-server-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 1,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      },
      {
        "current": {
          "selected": false,
          "text": "'.$host.'",
          "value": "'.$host.'"
        },
        "datasource": "Prometheus",
        "definition": "label_values(pg_up{project=~\"$project\",server=~\"$server\"}, instance)",
        "hide": 2,
        "includeAll": false,
        "label": "Host",
        "multi": false,
        "name": "host",
        "options": [],
        "query": {
          "query": "label_values(pg_up{project=~\"$project\",server=~\"$server\"}, instance)",
          "refId": "Prometheus-host-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 1,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      },
      {
        "allValue": ".*",
        "current": {
          "selected": false,
          "text": "All",
          "value": "$__all"
        },
        "datasource": "Prometheus",
        "definition": "",
        "hide": 2,
        "includeAll": true,
        "label": "DB",
        "multi": false,
        "name": "db",
        "options": [],
        "query": {
          "query": "label_values(pg_stat_database_tup_fetched{instance=~\"'.$host.'\",datname!~\"template.*|postgres\"},datname)",
          "refId": "Prometheus-db-Variable-Query"
        },
        "refresh": 2,
        "regex": "",
        "skipUrlSync": false,
        "sort": 0,
        "tagValuesQuery": "",
        "tagsQuery": "",
        "type": "query",
        "useTags": false
      }
	]
	}
}
}';
		return $res;
	}
}
