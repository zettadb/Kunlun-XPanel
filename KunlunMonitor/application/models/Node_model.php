<?php
class Node_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function nodeJSON($node)
	{
		$res='{
	"dashboard": {
	"id": null,
	"uid": null,
	"title": "node",
	"tags": [],
	"timezone": "browser",
	"schemaVersion": 16,
	"version": 1,
	"refresh": "5s",
	"links": [
		{
		  "asDropdown": false,
		  "icon": "info",
		  "includeVars": false,
		  "tags": [
			"$node"
		  ],
		  "targetBlank": true,
		  "title": "服务器IP：$node",
		  "type": "link",
		  "url": ""
		},
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "更新node_exporter",
		  "tooltip": "",
		  "type": "link",
		  "url": "https://github.com/prometheus/node_exporter/releases"
		},
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "更新当前仪表板",
		  "tooltip": "",
		  "type": "link",
		  "url": "https://grafana.com/dashboards/8919"
		},
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "StarsL\'s Blog",
		  "tooltip": "",
		  "type": "link",
		  "url": "https://starsl.cn"
		}
],
	"panels": [
	{
	"cacheTimeout": null,
	"datasource": "Prometheus",
	"description": "",
	"fieldConfig": {
	"defaults": {
	"color": {
	"mode": "thresholds"
	},
	"decimals": 1,
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
						  "color": "rgba(245, 54, 54, 0.9)",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 1
					  },
					  {
						  "color": "rgba(50, 172, 45, 0.97)",
						"value": 2
					  }
					]
				  },
				  "unit": "s"
				},
				"overrides": []
			  },
			  "gridPos": {
		"h": 3,
				"w": 2,
				"x": 0,
				"y": 0
			  },
			  "hideTimeOverride": true,
			  "id": 15,
			  "interval": null,
			  "links": [],
			  "maxDataPoints": 100,
			  "options": {
		"colorMode": "value",
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
					"expr": "sum(time() - node_boot_time_seconds{instance=~\"$node\"})",
				  "format": "time_series",
				  "hide": false,
				  "instant": true,
				  "intervalFactor": 1,
				  "refId": "A",
				  "step": 40
				}
			  ],
			  "title": "系统运行时间",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
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
						  "color": "rgba(245, 54, 54, 0.9)",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 2
					  },
					  {
						  "color": "rgba(50, 172, 45, 0.97)",
						"value": 3
					  }
					]
				  },
				  "unit": "bytes"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 2,
				"y": 0
			  },
			  "id": 75,
			  "interval": null,
			  "links": [],
			  "maxDataPoints": 100,
			  "options": {
				"colorMode": "value",
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
					"expr": "sum(node_memory_MemTotal_bytes{instance=~\"$node\"})",
				  "format": "time_series",
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "title": "内存总量",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 4,
				"y": 0
			  },
			  "id": 176,
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
					"expr": "100 - (avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"idle\"}[30m])) * 100)",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "总CPU使用率",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 6,
				"y": 0
			  },
			  "id": 179,
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
					"expr": "(1 - (node_memory_MemAvailable_bytes{instance=~\"$node\"} / (node_memory_MemTotal_bytes{instance=~\"$node\"})))* 100",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "内存使用率",
			  "type": "stat"
			},
			{
				"columns": [],
			  "datasource": "Prometheus",
			  "fontSize": "110%",
			  "gridPos": {
				"h": 6,
				"w": 10,
				"x": 8,
				"y": 0
			  },
			  "id": 164,
			  "links": [],
			  "pageSize": null,
			  "scroll": true,
			  "showHeader": true,
			  "sort": {
				"col": 6,
				"desc": false
			  },
			  "styles": [
				{
					"alias": "分区",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(50, 172, 45, 0.97)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(245, 54, 54, 0.9)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "mappingType": 1,
				  "pattern": "mountpoint",
				  "thresholds": [
					""
				],
				  "type": "string",
				  "unit": "bytes"
				},
				{
					"alias": "可用空间",
				  "align": "auto",
				  "colorMode": "value",
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "mappingType": 1,
				  "pattern": "Value #A",
				  "thresholds": [
					"10000000000",
					"20000000000"
				],
				  "type": "number",
				  "unit": "bytes"
				},
				{
					"alias": "使用率",
				  "align": "auto",
				  "colorMode": "cell",
				  "colors": [
					"rgba(50, 172, 45, 0.97)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(245, 54, 54, 0.9)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "mappingType": 1,
				  "pattern": "Value #B",
				  "thresholds": [
					"0.6",
					"0.8"
				],
				  "type": "number",
				  "unit": "percentunit"
				},
				{
					"alias": "总空间",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 1,
				  "link": false,
				  "mappingType": 1,
				  "pattern": "Value #C",
				  "thresholds": [],
				  "type": "number",
				  "unit": "bytes"
				},
				{
					"alias": "文件系统",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "link": false,
				  "mappingType": 1,
				  "pattern": "fstype",
				  "thresholds": [],
				  "type": "string",
				  "unit": "short"
				},
				{
					"alias": "IP",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "link": false,
				  "mappingType": 1,
				  "pattern": "instance",
				  "preserveFormat": false,
				  "sanitize": false,
				  "thresholds": [],
				  "type": "string",
				  "unit": "short"
				},
				{
					"alias": "",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "decimals": 2,
				  "pattern": "/.*/",
				  "preserveFormat": true,
				  "sanitize": false,
				  "thresholds": [],
				  "type": "hidden",
				  "unit": "short"
				}
			  ],
			  "targets": [
				{
					"expr": "node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"}-0",
				  "format": "table",
				  "hide": false,
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "C"
				},
				{
					"expr": "node_filesystem_avail_bytes {instance=~\'$node\',fstype=~\"ext4|xfs\"}-0",
				  "format": "table",
				  "hide": false,
				  "instant": true,
				  "interval": "10s",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A"
				},
				{
					"expr": "1-(node_filesystem_free_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"} / node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"})",
				  "format": "table",
				  "hide": false,
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "B"
				}
			  ],
			  "title": "各分区可用空间",
			  "transform": "table",
			  "type": "table-old"
			},
			{
				"aliasColors": {
				"filefd_192.168.200.241:9100": "super-light-green",
				"switches_192.168.200.241:9100": "semi-dark-red"
			  },
			  "bars": false,
			  "cacheTimeout": null,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 0,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 6,
				"w": 6,
				"x": 18,
				"y": 0
			  },
			  "hiddenSeries": false,
			  "hideTimeOverride": false,
			  "id": 16,
			  "legend": {
				"alignAsTable": false,
				"avg": false,
				"current": true,
				"max": true,
				"min": false,
				"rightSide": false,
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
			  "pointradius": 1,
			  "points": false,
			  "renderer": "flot",
			  "seriesOverrides": [
				{
					"alias": "/filefd_.*/",
				  "lines": false,
				  "pointradius": 1,
				  "points": true
				},
				{
					"alias": "/switches_.*/",
				  "color": "#F2495C",
				  "yaxis": 2
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_filefd_allocated{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 5,
				  "legendFormat": "filefd_{{instance}}",
				  "refId": "B"
				},
				{
					"expr": "irate(node_context_switches_total{instance=~\"$node\"}[30m])",
				  "intervalFactor": 5,
				  "legendFormat": "switches_{{instance}}",
				  "refId": "A"
				},
				{
					"expr": "node_filefd_maximum{instance=~\"$node\"}",
				  "hide": true,
				  "refId": "C"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "打开的文件描述符(左 )/每秒上下文切换次数(右)",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "label": "",
				  "logBase": 1,
				  "max": null,
				  "min": null,
				  "show": true
				},
				{
					"format": "short",
				  "label": "context_switches",
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
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "rgba(245, 54, 54, 0.9)",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 1
					  },
					  {
						  "color": "rgba(50, 172, 45, 0.97)",
						"value": 2
					  }
					]
				  },
				  "unit": "short"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 0,
				"y": 3
			  },
			  "id": 14,
			  "interval": null,
			  "links": [],
			  "maxDataPoints": 100,
			  "options": {
				"colorMode": "value",
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
					"expr": "sum(count(node_cpu_seconds_total{instance=~\"$node\", mode=\'system\'}) by (cpu))",
				  "format": "time_series",
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "title": "CPU 核数",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 20
					  },
					  {
						  "color": "#d44a3a",
						"value": 50
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 2,
				"y": 3
			  },
			  "id": 20,
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
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"iowait\"}[30m])) * 100",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "CPU iowait",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 4,
				"y": 3
			  },
			  "id": 177,
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
					"expr": "100 - ((node_filesystem_avail_bytes{instance=~\"$node\",mountpoint=\"$maxmount\",fstype=~\"ext4|xfs\"} * 100) / node_filesystem_size_bytes {instance=~\"$node\",mountpoint=\"$maxmount\",fstype=~\"ext4|xfs\"})",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "最大分区($maxmount)使用率",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
				  "mappings": [
					{
						"options": {
						"match": "null",
						"result": {
							"color": "#299c46",
						  "text": "0%"
						}
					  },
					  "type": "special"
					}
				  ],
				  "thresholds": {
						"mode": "absolute",
					"steps": [
					  {
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 6,
				"y": 3
			  },
			  "id": 178,
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
					"expr": "(1 - (node_memory_SwapFree_bytes{instance=~\"$node\"} / node_memory_SwapTotal_bytes{instance=~\"$node\"})) * 100",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "交换分区使用率",
			  "type": "stat"
			},
			{
				"aliasColors": {
				"15分钟": "#6ED0E0",
				"1分钟": "#BF1B00",
				"5分钟": "#CCA300"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "editable": true,
			  "error": false,
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "grid": {},
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 0,
				"y": 6
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 13,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "maxPerRow": 6,
			  "nullPointMode": "null as zero",
			  "options": {
				"alertThreshold": true
			  },
			  "percentage": false,
			  "pluginVersion": "8.2.1",
			  "pointradius": 5,
			  "points": false,
			  "renderer": "flot",
			  "repeat": null,
			  "seriesOverrides": [],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_load1{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_1m",
				  "metric": "",
				  "refId": "A",
				  "step": 20,
				  "target": ""
				},
				{
					"expr": "node_load5{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_5m",
				  "refId": "B",
				  "step": 20
				},
				{
					"expr": "node_load15{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_15m",
				  "refId": "C",
				  "step": 20
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "系统平均负载",
			  "tooltip": {
				"msResolution": false,
				"shared": true,
				"sort": 2,
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
				  "logBase": 1,
				  "max": null,
				  "min": null,
				  "show": true
				},
				{
					"format": "short",
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
				"aliasColors": {
				"192.168.200.241:9100_Total": "dark-red",
				"Idle - Waiting for something to happen": "#052B51",
				"guest": "#9AC48A",
				"idle": "#052B51",
				"iowait": "#EAB839",
				"irq": "#BF1B00",
				"nice": "#C15C17",
				"sdb_每秒I/O操作%": "#d683ce",
				"softirq": "#E24D42",
				"steal": "#FCE2DE",
				"system": "#508642",
				"user": "#5195CE",
				"磁盘花费在I/O操作占比": "#ba43a9"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "decimals": 2,
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 0,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 8,
				"y": 6
			  },
			  "hiddenSeries": false,
			  "id": 7,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
				"sort": "current",
				"sortDesc": true,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "maxPerRow": 6,
			  "nullPointMode": "null",
			  "options": {
				"alertThreshold": true
			  },
			  "percentage": false,
			  "pluginVersion": "8.2.1",
			  "pointradius": 5,
			  "points": false,
			  "renderer": "flot",
			  "repeat": null,
			  "seriesOverrides": [
				{
					"alias": "/.*_Total/",
				  "color": "#C4162A",
				  "fill": 0
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"system\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_System",
				  "refId": "A",
				  "step": 20
				},
				{
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"user\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_User",
				  "refId": "B",
				  "step": 240
				},
				{
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"iowait\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_Iowait",
				  "refId": "D",
				  "step": 240
				},
				{
					"expr": "1 - avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"idle\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_Total",
				  "refId": "F",
				  "step": 240
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "CPU使用率",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "format": "percentunit",
				  "label": "",
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
				"aliasColors": {
				"192.168.10.227:9100_em1_in下载": "super-light-green",
				"192.168.10.227:9100_em1_out上传": "dark-blue"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 3,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 16,
				"y": 6
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 157,
			  "legend": {
				"alignAsTable": true,
				"avg": false,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sort": "current",
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
			  "pointradius": 2,
			  "points": false,
			  "renderer": "flot",
			  "seriesOverrides": [
				{
					"alias": "/.*_out上传$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_network_receive_bytes_total{instance=~\'$node\',device!~\'tap.*|veth.*|br.*|docker.*|virbr*|lo*\'}[30m])*8",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_in下载",
				  "refId": "A",
				  "step": 4
				},
				{
					"expr": "irate(node_network_transmit_bytes_total{instance=~\'$node\',device!~\'tap.*|veth.*|br.*|docker.*|virbr*|lo*\'}[30m])*8",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_out上传",
				  "refId": "B",
				  "step": 4
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "网络流量",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"format": "bps",
				  "label": "上传（-）/下载（+）",
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
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 3,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 0,
				"y": 14
			  },
			  "hiddenSeries": false,
			  "id": 174,
			  "legend": {
				"alignAsTable": true,
				"avg": false,
				"current": true,
				"hideEmpty": false,
				"hideZero": false,
				"max": false,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
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
					"alias": "/Inodes.*/",
				  "yaxis": 2
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "1-(node_filesystem_free_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"} / node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"})",
				  "format": "time_series",
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}：{{mountpoint}}",
				  "refId": "A"
				},
				{
					"expr": "node_filesystem_files_free{instance=~\'$node\',fstype=~\"ext4|xfs\"} / node_filesystem_files{instance=~\'$node\',fstype=~\"ext4|xfs\"}",
				  "hide": true,
				  "legendFormat": "Inodes：{{instance}}：{{mountpoint}}",
				  "refId": "B"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "分时磁盘使用率",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "format": "percentunit",
				  "label": "",
				  "logBase": 1,
				  "max": null,
				  "min": null,
				  "show": true
				},
				{
					"decimals": 2,
				  "format": "percentunit",
				  "label": null,
				  "logBase": 1,
				  "max": "1",
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
				"aliasColors": {
				"192.168.200.241:9100_总内存": "dark-red",
				"内存_Avaliable": "#6ED0E0",
				"内存_Cached": "#EF843C",
				"内存_Free": "#629E51",
				"内存_Total": "#6d1f62",
				"内存_Used": "#eab839",
				"可用": "#9ac48a",
				"总内存": "#bf1b00"
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
			  "fill": 1,
			  "fillGradient": 0,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 8,
				"y": 14
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 156,
			  "legend": {
				"alignAsTable": true,
				"avg": false,
				"current": true,
				"max": false,
				"min": false,
				"rightSide": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_总内存/",
				  "color": "#C4162A",
				  "fill": 0
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_memory_MemTotal_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_总内存",
				  "refId": "A",
				  "step": 4
				},
				{
					"expr": "node_memory_MemTotal_bytes{instance=~\"$node\"} - node_memory_MemAvailable_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_已用",
				  "refId": "B",
				  "step": 4
				},
				{
					"expr": "node_memory_MemAvailable_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_可用",
				  "refId": "F",
				  "step": 4
				},
				{
					"expr": "node_memory_Buffers_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "内存_Buffers",
				  "refId": "D",
				  "step": 4
				},
				{
					"expr": "node_memory_MemFree_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "内存_Free",
				  "refId": "C",
				  "step": 4
				},
				{
					"expr": "node_memory_Cached_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "内存_Cached",
				  "refId": "E",
				  "step": 4
				},
				{
					"expr": "node_memory_MemTotal_bytes{instance=~\"$node\"} - (node_memory_Cached_bytes{instance=~\"$node\"} + node_memory_Buffers_bytes{instance=~\"$node\"} + node_memory_MemFree_bytes{instance=~\"$node\"})",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "refId": "G"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "内存信息",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "show": true
				}
			  ],
			  "yaxis": {
				"align": false,
				"alignLevel": null
			  }
			},
			{
				"aliasColors": {
				"Idle - Waiting for something to happen": "#052B51",
				"guest": "#9AC48A",
				"idle": "#052B51",
				"iowait": "#EAB839",
				"irq": "#BF1B00",
				"nice": "#C15C17",
				"sdb_每秒I/O操作%": "#d683ce",
				"softirq": "#E24D42",
				"steal": "#FCE2DE",
				"system": "#508642",
				"user": "#5195CE",
				"磁盘花费在I/O操作占比": "#ba43a9"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "decimals": null,
			  "description": "每一秒钟的自然时间内，花费在I/O上的耗时。（wall-clock time）\n\nnode_disk_io_time_seconds_total：\n磁盘花费在输入/输出操作上的秒数。该值为累加值。（Milliseconds Spent Doing I/Os）\n\nirate(node_disk_io_time_seconds_total[1m])：\n计算每秒的速率：(last值-last前一个值)/时间戳差值，即：1秒钟内磁盘花费在I/O操作的时间占比。",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 5,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 16,
				"y": 14
			  },
			  "hiddenSeries": false,
			  "id": 175,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
				"sort": null,
				"sortDesc": null,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "maxPerRow": 6,
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
					"expr": "irate(node_disk_io_time_seconds_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_每秒I/O操作%",
				  "refId": "C"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "每1秒内I/O操作耗费的时间",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"decimals": null,
				  "format": "s",
				  "label": "",
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
				"aliasColors": {
				"vda_write": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Reads completed: 每个磁盘分区每秒读完成次数\n\nWrites completed: 每个磁盘分区每秒写完成次数\n\nIO now 每个磁盘分区每秒正在处理的输入/输出请求数",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 9,
				"w": 8,
				"x": 0,
				"y": 22
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 161,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_读取$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_disk_reads_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_读取",
				  "refId": "A",
				  "step": 10
				},
				{
					"expr": "irate(node_disk_writes_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_写入",
				  "refId": "B",
				  "step": 10
				},
				{
					"expr": "node_disk_io_now{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{device}}",
				  "refId": "C"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "磁盘读写速率（IOPS）",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"decimals": null,
				  "format": "iops",
				  "label": "读取（-）/写入（+）I/O ops/sec",
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
				  "show": true
				}
			  ],
			  "yaxis": {
				"align": false,
				"alignLevel": null
			  }
			},
			{
				"aliasColors": {
				"vda_write": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Read bytes 每个磁盘分区每秒读取的比特数\nWritten bytes 每个磁盘分区每秒写入的比特数",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 9,
				"w": 8,
				"x": 8,
				"y": 22
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 168,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_读取$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_disk_read_bytes_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_读取",
				  "refId": "A",
				  "step": 10
				},
				{
					"expr": "irate(node_disk_written_bytes_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_写入",
				  "refId": "B",
				  "step": 10
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "每秒磁盘读写速度",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"decimals": null,
				  "format": "Bps",
				  "label": "读取（-）/写入（+）",
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
				"aliasColors": {
				"vda": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Read time seconds 每个磁盘分区读操作花费的秒数\n\nWrite time seconds 每个磁盘分区写操作花费的秒数\n\nIO time seconds 每个磁盘分区输入/输出操作花费的秒数\n\nIO time weighted seconds每个磁盘分区输入/输出操作花费的加权秒数",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 9,
				"w": 8,
				"x": 16,
				"y": 22
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 160,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"show": true,
				"sort": "current",
				"sortDesc": true,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "nullPointMode": "null as zero",
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
					"alias": "/,*_读取$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_disk_read_time_seconds_total{instance=~\"$node\"}[30m]) / irate(node_disk_reads_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_读取",
				  "refId": "B"
				},
				{
					"expr": "irate(node_disk_write_time_seconds_total{instance=~\"$node\"}[30m]) / irate(node_disk_writes_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_写入",
				  "refId": "C"
				},
				{
					"expr": "irate(node_disk_io_time_seconds_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": true,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{device}}",
				  "refId": "A",
				  "step": 10
				},
				{
					"expr": "irate(node_disk_io_time_weighted_seconds_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "{{device}}_加权",
				  "refId": "D"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "每次IO读写的耗时（参考：小于100ms）(beta)",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "label": "读取（-）/写入（+）",
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
				"aliasColors": {
				"192.168.200.241:9100_TCP_alloc": "semi-dark-blue",
				"TCP": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Sockets_used - 已使用的所有协议套接字总量\n\nCurrEstab - 当前状态为 ESTABLISHED 或 CLOSE-WAIT 的 TCP 连接数\n\nTCP_alloc - 已分配（已建立、已申请到sk_buff）的TCP套接字数量\n\nTCP_tw - 等待关闭的TCP连接数\n\nUDP_inuse - 正在使用的 UDP 套接字数量",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 0,
			  "gridPos": {
				"h": 12,
				"w": 12,
				"x": 0,
				"y": 31
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 158,
			  "interval": "",
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_Sockets_used/",
				  "color": "#C4162A",
				  "fill": 0
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_netstat_Tcp_CurrEstab{instance=~\'$node\'}",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_CurrEstab",
				  "refId": "A",
				  "step": 20
				},
				{
					"expr": "node_sockstat_TCP_tw{instance=~\'$node\'}",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_TCP_tw",
				  "refId": "D"
				},
				{
					"expr": "node_sockstat_sockets_used{instance=~\'$node\'}",
				  "legendFormat": "{{instance}}_Sockets_used",
				  "refId": "B"
				},
				{
					"expr": "node_sockstat_UDP_inuse{instance=~\'$node\'}",
				  "legendFormat": "{{instance}}_UDP_inuse",
				  "refId": "C"
				},
				{
					"expr": "node_sockstat_TCP_alloc{instance=~\'$node\'}",
				  "legendFormat": "{{instance}}_TCP_alloc",
				  "refId": "E"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "网络连接信息",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "min": null,
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
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 0,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 12,
				"w": 12,
				"x": 12,
				"y": 31
			  },
			  "hiddenSeries": false,
			  "id": 169,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
				"sort": "current",
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
			  "pointradius": 0.5,
			  "points": false,
			  "renderer": "flot",
			  "seriesOverrides": [],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_hwmon_temp_celsius{instance=~\'$node\'}",
				  "format": "time_series",
				  "intervalFactor": 10,
				  "legendFormat": "{{instance}}_{{chip}}_{{sensor}}",
				  "refId": "A"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "硬件温度(虚拟机可能无法显示该项)",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"format": "celsius",
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
				  "show": true
				}
			  ],
			  "yaxis": {
				"align": false,
				"alignLevel": null
			  }
			}
		],
	"templating": {
	"list": [
		  {
			  "allValue": null,
			"current": {
			  "selected": false,
			  "text": "node",
			  "value": "node"
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_uname_info, job)",
			"description": null,
			"error": null,
			"hide": 0,
			"includeAll": false,
			"label": "JOB",
			"multi": false,
			"name": "job",
			"options": [],
			"query": {
			  "query": "label_values(node_uname_info, job)",
			  "refId": "Prometheus-job-Variable-Query"
			},
			"refresh": 1,
			"regex": "",
			"skipUrlSync": false,
			"sort": 1,
			"tagValuesQuery": "",
			"tagsQuery": "",
			"type": "query",
			"useTags": false
		  },
		  {
			  "allValue": null,
			"current": {
			  "selected": false,
			  "text": "All",
			  "value": "$__all"
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_uname_info{job=~\"$job\"}, nodename)",
			"description": null,
			"error": null,
			"hide": 0,
			"includeAll": true,
			"label": "主机名",
			"multi": true,
			"name": "hostname",
			"options": [],
			"query": {
			  "query": "label_values(node_uname_info{job=~\"$job\"}, nodename)",
			  "refId": "Prometheus-hostname-Variable-Query"
			},
			"refresh": 1,
			"regex": "",
			"skipUrlSync": false,
			"sort": 0,
			"tagValuesQuery": "",
			"tagsQuery": "",
			"type": "query",
			"useTags": false
		  },
		  {
			  "allFormat": "glob",
			"allValue": null,
			"current": {
			  "selected": false,
			  "text": "All",
			  "value": "$__all"
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_uname_info{nodename=~\"$hostname\"},instance)",
			"description": null,
			"error": null,
			"hide": 0,
			"includeAll": true,
			"label": "IP( 自动关联主机名)",
			"multi": false,
			"multiFormat": "regex values",
			"name": "node",
			"options": [],
			"query": {
			  "query": "label_values(node_uname_info{nodename=~\"$hostname\"},instance)",
			  "refId": "Prometheus-node-Variable-Query"
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
			"allFormat": "glob",
			"allValue": null,
			"current": {
			  "isNone": true,
			  "selected": false,
			  "text": "None",
			  "value": ""
			},
			"datasource": "Prometheus",
			"definition": "",
			"description": null,
			"error": null,
			"hide": 2,
			"includeAll": false,
			"label": "环境",
			"multi": false,
			"multiFormat": "regex values",
			"name": "env",
			"options": [],
			"query": {
			  "query": "label_values(node_exporter_build_info,env)",
			  "refId": "Prometheus-env-Variable-Query"
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
			"allFormat": "glob",
			"allValue": "",
			"current": {
			  "isNone": true,
			  "selected": false,
			  "text": "None",
			  "value": ""
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_exporter_build_info{env=~\"$en\"},name)",
			"description": null,
			"error": null,
			"hide": 2,
			"includeAll": false,
			"label": "名称",
			"multi": true,
			"multiFormat": "regex values",
			"name": "name",
			"options": [],
			"query": {
			  "query": "label_values(node_exporter_build_info{env=~\"$env\"},name)",
			  "refId": "Prometheus-name-Variable-Query"
			},
			"refresh": 2,
			"regex": "",
			"skipUrlSync": false,
			"sort": 1,
			"tagsQuery": "",
			"type": "query",
			"useTags": false
		  },
		  {
			"allValue": null,
			"current": {
			  "selected": false,
			  "text": "/home",
			  "value": "/home"
			},
			"datasource": "Prometheus",
			"definition": "",
			"description": null,
			"error": null,
			"hide": 2,
			"includeAll": false,
			"label": "",
			"multi": false,
			"name": "maxmount",
			"options": [],
			"query": {
			  "query": "query_result(topk(1,sort_desc (max(node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"}) by (mountpoint))))",
			  "refId": "Prometheus-maxmount-Variable-Query"
			},
			"refresh": 2,
			"regex": "/.*\\\"(.*)\\\".*/",
			"skipUrlSync": false,
			"sort": 0,
			"tagValuesQuery": "",
			"tagsQuery": "",
			"type": "query",
			"useTags": false
		  }
	    ]
	  }
	},
	"folderId": 0,
	"overwrite": false
}';
		return $res;
	}

	public function updateNodeJSON($node,$id, $uid, $db_title, $db_version)
	{
		$res='{
	"dashboard": {
	"id": '.$id.',
	"uid": "'.$uid.'",
	"title": "'.$db_title.'",
	"version": '.$db_version.',
	"links": [
		{
		  "asDropdown": false,
		  "icon": "info",
		  "includeVars": false,
		  "tags": [
			"$node"
		  ],
		  "targetBlank": true,
		  "title": "服务器IP：$node",
		  "type": "link",
		  "url": ""
		},
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "更新node_exporter",
		  "tooltip": "",
		  "type": "link",
		  "url": "https://github.com/prometheus/node_exporter/releases"
		},
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "更新当前仪表板",
		  "tooltip": "",
		  "type": "link",
		  "url": "https://grafana.com/dashboards/8919"
		},
		{
		  "icon": "external link",
		  "tags": [],
		  "targetBlank": true,
		  "title": "StarsL\'s Blog",
		  "tooltip": "",
		  "type": "link",
		  "url": "https://starsl.cn"
		}
],
	"panels": [
	{
	"cacheTimeout": null,
	"datasource": "Prometheus",
	"description": "",
	"fieldConfig": {
	"defaults": {
	"color": {
	"mode": "thresholds"
	},
	"decimals": 1,
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
						  "color": "rgba(245, 54, 54, 0.9)",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 1
					  },
					  {
						  "color": "rgba(50, 172, 45, 0.97)",
						"value": 2
					  }
					]
				  },
				  "unit": "s"
				},
				"overrides": []
			  },
			  "gridPos": {
		"h": 3,
				"w": 2,
				"x": 0,
				"y": 0
			  },
			  "hideTimeOverride": true,
			  "id": 15,
			  "interval": null,
			  "links": [],
			  "maxDataPoints": 100,
			  "options": {
		"colorMode": "value",
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
					"expr": "sum(time() - node_boot_time_seconds{instance=~\"$node\"})",
				  "format": "time_series",
				  "hide": false,
				  "instant": true,
				  "intervalFactor": 1,
				  "refId": "A",
				  "step": 40
				}
			  ],
			  "title": "系统运行时间",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
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
						  "color": "rgba(245, 54, 54, 0.9)",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 2
					  },
					  {
						  "color": "rgba(50, 172, 45, 0.97)",
						"value": 3
					  }
					]
				  },
				  "unit": "bytes"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 2,
				"y": 0
			  },
			  "id": 75,
			  "interval": null,
			  "links": [],
			  "maxDataPoints": 100,
			  "options": {
				"colorMode": "value",
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
					"expr": "sum(node_memory_MemTotal_bytes{instance=~\"$node\"})",
				  "format": "time_series",
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "title": "内存总量",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 4,
				"y": 0
			  },
			  "id": 176,
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
					"expr": "100 - (avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"idle\"}[30m])) * 100)",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "总CPU使用率",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 6,
				"y": 0
			  },
			  "id": 179,
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
					"expr": "(1 - (node_memory_MemAvailable_bytes{instance=~\"$node\"} / (node_memory_MemTotal_bytes{instance=~\"$node\"})))* 100",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "内存使用率",
			  "type": "stat"
			},
			{
				"columns": [],
			  "datasource": "Prometheus",
			  "fontSize": "110%",
			  "gridPos": {
				"h": 6,
				"w": 10,
				"x": 8,
				"y": 0
			  },
			  "id": 164,
			  "links": [],
			  "pageSize": null,
			  "scroll": true,
			  "showHeader": true,
			  "sort": {
				"col": 6,
				"desc": false
			  },
			  "styles": [
				{
					"alias": "分区",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(50, 172, 45, 0.97)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(245, 54, 54, 0.9)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "mappingType": 1,
				  "pattern": "mountpoint",
				  "thresholds": [
					""
				],
				  "type": "string",
				  "unit": "bytes"
				},
				{
					"alias": "可用空间",
				  "align": "auto",
				  "colorMode": "value",
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "mappingType": 1,
				  "pattern": "Value #A",
				  "thresholds": [
					"10000000000",
					"20000000000"
				],
				  "type": "number",
				  "unit": "bytes"
				},
				{
					"alias": "使用率",
				  "align": "auto",
				  "colorMode": "cell",
				  "colors": [
					"rgba(50, 172, 45, 0.97)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(245, 54, 54, 0.9)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "mappingType": 1,
				  "pattern": "Value #B",
				  "thresholds": [
					"0.6",
					"0.8"
				],
				  "type": "number",
				  "unit": "percentunit"
				},
				{
					"alias": "总空间",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 1,
				  "link": false,
				  "mappingType": 1,
				  "pattern": "Value #C",
				  "thresholds": [],
				  "type": "number",
				  "unit": "bytes"
				},
				{
					"alias": "文件系统",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "link": false,
				  "mappingType": 1,
				  "pattern": "fstype",
				  "thresholds": [],
				  "type": "string",
				  "unit": "short"
				},
				{
					"alias": "IP",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "dateFormat": "YYYY-MM-DD HH:mm:ss",
				  "decimals": 2,
				  "link": false,
				  "mappingType": 1,
				  "pattern": "instance",
				  "preserveFormat": false,
				  "sanitize": false,
				  "thresholds": [],
				  "type": "string",
				  "unit": "short"
				},
				{
					"alias": "",
				  "align": "auto",
				  "colorMode": null,
				  "colors": [
					"rgba(245, 54, 54, 0.9)",
					"rgba(237, 129, 40, 0.89)",
					"rgba(50, 172, 45, 0.97)"
				],
				  "decimals": 2,
				  "pattern": "/.*/",
				  "preserveFormat": true,
				  "sanitize": false,
				  "thresholds": [],
				  "type": "hidden",
				  "unit": "short"
				}
			  ],
			  "targets": [
				{
					"expr": "node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"}-0",
				  "format": "table",
				  "hide": false,
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "C"
				},
				{
					"expr": "node_filesystem_avail_bytes {instance=~\'$node\',fstype=~\"ext4|xfs\"}-0",
				  "format": "table",
				  "hide": false,
				  "instant": true,
				  "interval": "10s",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A"
				},
				{
					"expr": "1-(node_filesystem_free_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"} / node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"})",
				  "format": "table",
				  "hide": false,
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "B"
				}
			  ],
			  "title": "各分区可用空间",
			  "transform": "table",
			  "type": "table-old"
			},
			{
				"aliasColors": {
				"filefd_192.168.200.241:9100": "super-light-green",
				"switches_192.168.200.241:9100": "semi-dark-red"
			  },
			  "bars": false,
			  "cacheTimeout": null,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 0,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 6,
				"w": 6,
				"x": 18,
				"y": 0
			  },
			  "hiddenSeries": false,
			  "hideTimeOverride": false,
			  "id": 16,
			  "legend": {
				"alignAsTable": false,
				"avg": false,
				"current": true,
				"max": true,
				"min": false,
				"rightSide": false,
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
			  "pointradius": 1,
			  "points": false,
			  "renderer": "flot",
			  "seriesOverrides": [
				{
					"alias": "/filefd_.*/",
				  "lines": false,
				  "pointradius": 1,
				  "points": true
				},
				{
					"alias": "/switches_.*/",
				  "color": "#F2495C",
				  "yaxis": 2
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_filefd_allocated{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 5,
				  "legendFormat": "filefd_{{instance}}",
				  "refId": "B"
				},
				{
					"expr": "irate(node_context_switches_total{instance=~\"$node\"}[30m])",
				  "intervalFactor": 5,
				  "legendFormat": "switches_{{instance}}",
				  "refId": "A"
				},
				{
					"expr": "node_filefd_maximum{instance=~\"$node\"}",
				  "hide": true,
				  "refId": "C"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "打开的文件描述符(左 )/每秒上下文切换次数(右)",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "label": "",
				  "logBase": 1,
				  "max": null,
				  "min": null,
				  "show": true
				},
				{
					"format": "short",
				  "label": "context_switches",
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
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "rgba(245, 54, 54, 0.9)",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 1
					  },
					  {
						  "color": "rgba(50, 172, 45, 0.97)",
						"value": 2
					  }
					]
				  },
				  "unit": "short"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 0,
				"y": 3
			  },
			  "id": 14,
			  "interval": null,
			  "links": [],
			  "maxDataPoints": 100,
			  "options": {
				"colorMode": "value",
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
					"expr": "sum(count(node_cpu_seconds_total{instance=~\"$node\", mode=\'system\'}) by (cpu))",
				  "format": "time_series",
				  "instant": true,
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "title": "CPU 核数",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 20
					  },
					  {
						  "color": "#d44a3a",
						"value": 50
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 2,
				"y": 3
			  },
			  "id": 20,
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
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"iowait\"}[30m])) * 100",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "CPU iowait",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
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
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 4,
				"y": 3
			  },
			  "id": 177,
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
					"expr": "100 - ((node_filesystem_avail_bytes{instance=~\"$node\",mountpoint=\"$maxmount\",fstype=~\"ext4|xfs\"} * 100) / node_filesystem_size_bytes {instance=~\"$node\",mountpoint=\"$maxmount\",fstype=~\"ext4|xfs\"})",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "最大分区($maxmount)使用率",
			  "type": "stat"
			},
			{
				"cacheTimeout": null,
			  "datasource": "Prometheus",
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"color": {
						"mode": "thresholds"
				  },
				  "mappings": [
					{
						"options": {
						"match": "null",
						"result": {
							"color": "#299c46",
						  "text": "0%"
						}
					  },
					  "type": "special"
					}
				  ],
				  "thresholds": {
						"mode": "absolute",
					"steps": [
					  {
						  "color": "#299c46",
						"value": null
					  },
					  {
						  "color": "rgba(237, 129, 40, 0.89)",
						"value": 50
					  },
					  {
						  "color": "#d44a3a",
						"value": 80
					  }
					]
				  },
				  "unit": "percent"
				},
				"overrides": []
			  },
			  "gridPos": {
				"h": 3,
				"w": 2,
				"x": 6,
				"y": 3
			  },
			  "id": 178,
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
					"expr": "(1 - (node_memory_SwapFree_bytes{instance=~\"$node\"} / node_memory_SwapTotal_bytes{instance=~\"$node\"})) * 100",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "",
				  "refId": "A",
				  "step": 20
				}
			  ],
			  "timeFrom": null,
			  "timeShift": null,
			  "title": "交换分区使用率",
			  "type": "stat"
			},
			{
				"aliasColors": {
				"15分钟": "#6ED0E0",
				"1分钟": "#BF1B00",
				"5分钟": "#CCA300"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "editable": true,
			  "error": false,
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "grid": {},
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 0,
				"y": 6
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 13,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "maxPerRow": 6,
			  "nullPointMode": "null as zero",
			  "options": {
				"alertThreshold": true
			  },
			  "percentage": false,
			  "pluginVersion": "8.2.1",
			  "pointradius": 5,
			  "points": false,
			  "renderer": "flot",
			  "repeat": null,
			  "seriesOverrides": [],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_load1{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_1m",
				  "metric": "",
				  "refId": "A",
				  "step": 20,
				  "target": ""
				},
				{
					"expr": "node_load5{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_5m",
				  "refId": "B",
				  "step": 20
				},
				{
					"expr": "node_load15{instance=~\"$node\"}",
				  "format": "time_series",
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_15m",
				  "refId": "C",
				  "step": 20
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "系统平均负载",
			  "tooltip": {
				"msResolution": false,
				"shared": true,
				"sort": 2,
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
				  "logBase": 1,
				  "max": null,
				  "min": null,
				  "show": true
				},
				{
					"format": "short",
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
				"aliasColors": {
				"192.168.200.241:9100_Total": "dark-red",
				"Idle - Waiting for something to happen": "#052B51",
				"guest": "#9AC48A",
				"idle": "#052B51",
				"iowait": "#EAB839",
				"irq": "#BF1B00",
				"nice": "#C15C17",
				"sdb_每秒I/O操作%": "#d683ce",
				"softirq": "#E24D42",
				"steal": "#FCE2DE",
				"system": "#508642",
				"user": "#5195CE",
				"磁盘花费在I/O操作占比": "#ba43a9"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "decimals": 2,
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 0,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 8,
				"y": 6
			  },
			  "hiddenSeries": false,
			  "id": 7,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
				"sort": "current",
				"sortDesc": true,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "maxPerRow": 6,
			  "nullPointMode": "null",
			  "options": {
				"alertThreshold": true
			  },
			  "percentage": false,
			  "pluginVersion": "8.2.1",
			  "pointradius": 5,
			  "points": false,
			  "renderer": "flot",
			  "repeat": null,
			  "seriesOverrides": [
				{
					"alias": "/.*_Total/",
				  "color": "#C4162A",
				  "fill": 0
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"system\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_System",
				  "refId": "A",
				  "step": 20
				},
				{
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"user\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_User",
				  "refId": "B",
				  "step": 240
				},
				{
					"expr": "avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"iowait\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_Iowait",
				  "refId": "D",
				  "step": 240
				},
				{
					"expr": "1 - avg(irate(node_cpu_seconds_total{instance=~\"$node\",mode=\"idle\"}[30m])) by (instance)",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_Total",
				  "refId": "F",
				  "step": 240
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "CPU使用率",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "format": "percentunit",
				  "label": "",
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
				"aliasColors": {
				"192.168.10.227:9100_em1_in下载": "super-light-green",
				"192.168.10.227:9100_em1_out上传": "dark-blue"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 3,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 16,
				"y": 6
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 157,
			  "legend": {
				"alignAsTable": true,
				"avg": false,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sort": "current",
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
			  "pointradius": 2,
			  "points": false,
			  "renderer": "flot",
			  "seriesOverrides": [
				{
					"alias": "/.*_out上传$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_network_receive_bytes_total{instance=~\'$node\',device!~\'tap.*|veth.*|br.*|docker.*|virbr*|lo*\'}[30m])*8",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_in下载",
				  "refId": "A",
				  "step": 4
				},
				{
					"expr": "irate(node_network_transmit_bytes_total{instance=~\'$node\',device!~\'tap.*|veth.*|br.*|docker.*|virbr*|lo*\'}[30m])*8",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_out上传",
				  "refId": "B",
				  "step": 4
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "网络流量",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"format": "bps",
				  "label": "上传（-）/下载（+）",
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
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 3,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 0,
				"y": 14
			  },
			  "hiddenSeries": false,
			  "id": 174,
			  "legend": {
				"alignAsTable": true,
				"avg": false,
				"current": true,
				"hideEmpty": false,
				"hideZero": false,
				"max": false,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
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
					"alias": "/Inodes.*/",
				  "yaxis": 2
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "1-(node_filesystem_free_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"} / node_filesystem_size_bytes{instance=~\'$node\',fstype=~\"ext4|xfs\"})",
				  "format": "time_series",
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}：{{mountpoint}}",
				  "refId": "A"
				},
				{
					"expr": "node_filesystem_files_free{instance=~\'$node\',fstype=~\"ext4|xfs\"} / node_filesystem_files{instance=~\'$node\',fstype=~\"ext4|xfs\"}",
				  "hide": true,
				  "legendFormat": "Inodes：{{instance}}：{{mountpoint}}",
				  "refId": "B"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "分时磁盘使用率",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "format": "percentunit",
				  "label": "",
				  "logBase": 1,
				  "max": null,
				  "min": null,
				  "show": true
				},
				{
					"decimals": 2,
				  "format": "percentunit",
				  "label": null,
				  "logBase": 1,
				  "max": "1",
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
				"aliasColors": {
				"192.168.200.241:9100_总内存": "dark-red",
				"内存_Avaliable": "#6ED0E0",
				"内存_Cached": "#EF843C",
				"内存_Free": "#629E51",
				"内存_Total": "#6d1f62",
				"内存_Used": "#eab839",
				"可用": "#9ac48a",
				"总内存": "#bf1b00"
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
			  "fill": 1,
			  "fillGradient": 0,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 8,
				"y": 14
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 156,
			  "legend": {
				"alignAsTable": true,
				"avg": false,
				"current": true,
				"max": false,
				"min": false,
				"rightSide": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_总内存/",
				  "color": "#C4162A",
				  "fill": 0
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_memory_MemTotal_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_总内存",
				  "refId": "A",
				  "step": 4
				},
				{
					"expr": "node_memory_MemTotal_bytes{instance=~\"$node\"} - node_memory_MemAvailable_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_已用",
				  "refId": "B",
				  "step": 4
				},
				{
					"expr": "node_memory_MemAvailable_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_可用",
				  "refId": "F",
				  "step": 4
				},
				{
					"expr": "node_memory_Buffers_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "内存_Buffers",
				  "refId": "D",
				  "step": 4
				},
				{
					"expr": "node_memory_MemFree_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "内存_Free",
				  "refId": "C",
				  "step": 4
				},
				{
					"expr": "node_memory_Cached_bytes{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "内存_Cached",
				  "refId": "E",
				  "step": 4
				},
				{
					"expr": "node_memory_MemTotal_bytes{instance=~\"$node\"} - (node_memory_Cached_bytes{instance=~\"$node\"} + node_memory_Buffers_bytes{instance=~\"$node\"} + node_memory_MemFree_bytes{instance=~\"$node\"})",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "refId": "G"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "内存信息",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "show": true
				}
			  ],
			  "yaxis": {
				"align": false,
				"alignLevel": null
			  }
			},
			{
				"aliasColors": {
				"Idle - Waiting for something to happen": "#052B51",
				"guest": "#9AC48A",
				"idle": "#052B51",
				"iowait": "#EAB839",
				"irq": "#BF1B00",
				"nice": "#C15C17",
				"sdb_每秒I/O操作%": "#d683ce",
				"softirq": "#E24D42",
				"steal": "#FCE2DE",
				"system": "#508642",
				"user": "#5195CE",
				"磁盘花费在I/O操作占比": "#ba43a9"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "decimals": null,
			  "description": "每一秒钟的自然时间内，花费在I/O上的耗时。（wall-clock time）\n\nnode_disk_io_time_seconds_total：\n磁盘花费在输入/输出操作上的秒数。该值为累加值。（Milliseconds Spent Doing I/Os）\n\nirate(node_disk_io_time_seconds_total[1m])：\n计算每秒的速率：(last值-last前一个值)/时间戳差值，即：1秒钟内磁盘花费在I/O操作的时间占比。",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 5,
			  "gridPos": {
				"h": 8,
				"w": 8,
				"x": 16,
				"y": 14
			  },
			  "hiddenSeries": false,
			  "id": 175,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
				"sort": null,
				"sortDesc": null,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "maxPerRow": 6,
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
					"expr": "irate(node_disk_io_time_seconds_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_每秒I/O操作%",
				  "refId": "C"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "每1秒内I/O操作耗费的时间",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"decimals": null,
				  "format": "s",
				  "label": "",
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
				"aliasColors": {
				"vda_write": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Reads completed: 每个磁盘分区每秒读完成次数\n\nWrites completed: 每个磁盘分区每秒写完成次数\n\nIO now 每个磁盘分区每秒正在处理的输入/输出请求数",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 9,
				"w": 8,
				"x": 0,
				"y": 22
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 161,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_读取$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_disk_reads_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_读取",
				  "refId": "A",
				  "step": 10
				},
				{
					"expr": "irate(node_disk_writes_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_写入",
				  "refId": "B",
				  "step": 10
				},
				{
					"expr": "node_disk_io_now{instance=~\"$node\"}",
				  "format": "time_series",
				  "hide": true,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{device}}",
				  "refId": "C"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "磁盘读写速率（IOPS）",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"decimals": null,
				  "format": "iops",
				  "label": "读取（-）/写入（+）I/O ops/sec",
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
				  "show": true
				}
			  ],
			  "yaxis": {
				"align": false,
				"alignLevel": null
			  }
			},
			{
				"aliasColors": {
				"vda_write": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Read bytes 每个磁盘分区每秒读取的比特数\nWritten bytes 每个磁盘分区每秒写入的比特数",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 9,
				"w": 8,
				"x": 8,
				"y": 22
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 168,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_读取$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_disk_read_bytes_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_读取",
				  "refId": "A",
				  "step": 10
				},
				{
					"expr": "irate(node_disk_written_bytes_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_写入",
				  "refId": "B",
				  "step": 10
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "每秒磁盘读写速度",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"decimals": null,
				  "format": "Bps",
				  "label": "读取（-）/写入（+）",
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
				"aliasColors": {
				"vda": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Read time seconds 每个磁盘分区读操作花费的秒数\n\nWrite time seconds 每个磁盘分区写操作花费的秒数\n\nIO time seconds 每个磁盘分区输入/输出操作花费的秒数\n\nIO time weighted seconds每个磁盘分区输入/输出操作花费的加权秒数",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 9,
				"w": 8,
				"x": 16,
				"y": 22
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 160,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"show": true,
				"sort": "current",
				"sortDesc": true,
				"total": false,
				"values": true
			  },
			  "lines": true,
			  "linewidth": 2,
			  "links": [],
			  "nullPointMode": "null as zero",
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
					"alias": "/,*_读取$/",
				  "transform": "negative-Y"
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "irate(node_disk_read_time_seconds_total{instance=~\"$node\"}[30m]) / irate(node_disk_reads_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_读取",
				  "refId": "B"
				},
				{
					"expr": "irate(node_disk_write_time_seconds_total{instance=~\"$node\"}[30m]) / irate(node_disk_writes_completed_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_{{device}}_写入",
				  "refId": "C"
				},
				{
					"expr": "irate(node_disk_io_time_seconds_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": true,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{device}}",
				  "refId": "A",
				  "step": 10
				},
				{
					"expr": "irate(node_disk_io_time_weighted_seconds_total{instance=~\"$node\"}[30m])",
				  "format": "time_series",
				  "hide": true,
				  "intervalFactor": 1,
				  "legendFormat": "{{device}}_加权",
				  "refId": "D"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "每次IO读写的耗时（参考：小于100ms）(beta)",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "label": "读取（-）/写入（+）",
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
				"aliasColors": {
				"192.168.200.241:9100_TCP_alloc": "semi-dark-blue",
				"TCP": "#6ED0E0"
			  },
			  "bars": false,
			  "dashLength": 10,
			  "dashes": false,
			  "datasource": "Prometheus",
			  "description": "Sockets_used - 已使用的所有协议套接字总量\n\nCurrEstab - 当前状态为 ESTABLISHED 或 CLOSE-WAIT 的 TCP 连接数\n\nTCP_alloc - 已分配（已建立、已申请到sk_buff）的TCP套接字数量\n\nTCP_tw - 等待关闭的TCP连接数\n\nUDP_inuse - 正在使用的 UDP 套接字数量",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 1,
			  "fillGradient": 0,
			  "gridPos": {
				"h": 12,
				"w": 12,
				"x": 0,
				"y": 31
			  },
			  "height": "300",
			  "hiddenSeries": false,
			  "id": 158,
			  "interval": "",
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sort": "current",
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
					"alias": "/.*_Sockets_used/",
				  "color": "#C4162A",
				  "fill": 0
				}
			  ],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_netstat_Tcp_CurrEstab{instance=~\'$node\'}",
				  "format": "time_series",
				  "hide": false,
				  "instant": false,
				  "interval": "",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_CurrEstab",
				  "refId": "A",
				  "step": 20
				},
				{
					"expr": "node_sockstat_TCP_tw{instance=~\'$node\'}",
				  "format": "time_series",
				  "intervalFactor": 1,
				  "legendFormat": "{{instance}}_TCP_tw",
				  "refId": "D"
				},
				{
					"expr": "node_sockstat_sockets_used{instance=~\'$node\'}",
				  "legendFormat": "{{instance}}_Sockets_used",
				  "refId": "B"
				},
				{
					"expr": "node_sockstat_UDP_inuse{instance=~\'$node\'}",
				  "legendFormat": "{{instance}}_UDP_inuse",
				  "refId": "C"
				},
				{
					"expr": "node_sockstat_TCP_alloc{instance=~\'$node\'}",
				  "legendFormat": "{{instance}}_TCP_alloc",
				  "refId": "E"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "网络连接信息",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
				  "min": null,
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
			  "description": "",
			  "fieldConfig": {
				"defaults": {
					"links": []
				},
				"overrides": []
			  },
			  "fill": 0,
			  "fillGradient": 1,
			  "gridPos": {
				"h": 12,
				"w": 12,
				"x": 12,
				"y": 31
			  },
			  "hiddenSeries": false,
			  "id": 169,
			  "legend": {
				"alignAsTable": true,
				"avg": true,
				"current": true,
				"hideEmpty": true,
				"hideZero": true,
				"max": true,
				"min": false,
				"rightSide": false,
				"show": true,
				"sideWidth": null,
				"sort": "current",
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
			  "pointradius": 0.5,
			  "points": false,
			  "renderer": "flot",
			  "seriesOverrides": [],
			  "spaceLength": 10,
			  "stack": false,
			  "steppedLine": false,
			  "targets": [
				{
					"expr": "node_hwmon_temp_celsius{instance=~\'$node\'}",
				  "format": "time_series",
				  "intervalFactor": 10,
				  "legendFormat": "{{instance}}_{{chip}}_{{sensor}}",
				  "refId": "A"
				}
			  ],
			  "thresholds": [],
			  "timeFrom": null,
			  "timeRegions": [],
			  "timeShift": null,
			  "title": "硬件温度(虚拟机可能无法显示该项)",
			  "tooltip": {
				"shared": true,
				"sort": 2,
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
					"format": "celsius",
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
				  "show": true
				}
			  ],
			  "yaxis": {
				"align": false,
				"alignLevel": null
			  }
			}
		],
	"templating": {
	"list": [
		  {
			  "allValue": null,
			"current": {
			  "selected": false,
			  "text": "node",
			  "value": "node"
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_uname_info, job)",
			"description": null,
			"error": null,
			"hide": 0,
			"includeAll": false,
			"label": "JOB",
			"multi": false,
			"name": "job",
			"options": [],
			"query": {
			  "query": "label_values(node_uname_info, job)",
			  "refId": "Prometheus-job-Variable-Query"
			},
			"refresh": 1,
			"regex": "",
			"skipUrlSync": false,
			"sort": 1,
			"tagValuesQuery": "",
			"tagsQuery": "",
			"type": "query",
			"useTags": false
		  },
		  {
			  "allValue": null,
			"current": {
			  "selected": false,
			  "text": "All",
			  "value": "$__all"
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_uname_info{job=~\"$job\"}, nodename)",
			"description": null,
			"error": null,
			"hide": 0,
			"includeAll": true,
			"label": "主机名",
			"multi": true,
			"name": "hostname",
			"options": [],
			"query": {
			  "query": "label_values(node_uname_info{job=~\"$job\"}, nodename)",
			  "refId": "Prometheus-hostname-Variable-Query"
			},
			"refresh": 1,
			"regex": "",
			"skipUrlSync": false,
			"sort": 0,
			"tagValuesQuery": "",
			"tagsQuery": "",
			"type": "query",
			"useTags": false
		  },
		  {
			  "allFormat": "glob",
			"allValue": null,
			"current": {
			  "selected": false,
			  "text": "All",
			  "value": "$__all"
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_uname_info{nodename=~\"$hostname\"},instance)",
			"description": null,
			"error": null,
			"hide": 0,
			"includeAll": true,
			"label": "IP( 自动关联主机名)",
			"multi": false,
			"multiFormat": "regex values",
			"name": "node",
			"options": [],
			"query": {
			  "query": "label_values(node_uname_info{nodename=~\"$hostname\"},instance)",
			  "refId": "Prometheus-node-Variable-Query"
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
			"allFormat": "glob",
			"allValue": null,
			"current": {
			  "isNone": true,
			  "selected": false,
			  "text": "None",
			  "value": ""
			},
			"datasource": "Prometheus",
			"definition": "",
			"description": null,
			"error": null,
			"hide": 2,
			"includeAll": false,
			"label": "环境",
			"multi": false,
			"multiFormat": "regex values",
			"name": "env",
			"options": [],
			"query": {
			  "query": "label_values(node_exporter_build_info,env)",
			  "refId": "Prometheus-env-Variable-Query"
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
			"allFormat": "glob",
			"allValue": "",
			"current": {
			  "isNone": true,
			  "selected": false,
			  "text": "None",
			  "value": ""
			},
			"datasource": "Prometheus",
			"definition": "label_values(node_exporter_build_info{env=~\"$en\"},name)",
			"description": null,
			"error": null,
			"hide": 2,
			"includeAll": false,
			"label": "名称",
			"multi": true,
			"multiFormat": "regex values",
			"name": "name",
			"options": [],
			"query": {
			  "query": "label_values(node_exporter_build_info{env=~\"$env\"},name)",
			  "refId": "Prometheus-name-Variable-Query"
			},
			"refresh": 2,
			"regex": "",
			"skipUrlSync": false,
			"sort": 1,
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
