// Generates simple flat app icons (rounded-square background + progress-ring mark)
// as raw PNGs, with no external dependencies (uses Node's built-in zlib).
import { deflateSync } from 'node:zlib'
import { writeFileSync, mkdirSync } from 'node:fs'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = dirname(fileURLToPath(import.meta.url))
const outDir = join(__dirname, '..', 'public')
mkdirSync(outDir, { recursive: true })

const BG = [255, 56, 92] // Airbnb coral #FF385C
const FG = [255, 255, 255]

function crc32(buf) {
  let table = crc32.table
  if (!table) {
    table = crc32.table = new Uint32Array(256)
    for (let n = 0; n < 256; n++) {
      let c = n
      for (let k = 0; k < 8; k++) c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1
      table[n] = c >>> 0
    }
  }
  let crc = 0xffffffff
  for (let i = 0; i < buf.length; i++) crc = table[(crc ^ buf[i]) & 0xff] ^ (crc >>> 8)
  return (crc ^ 0xffffffff) >>> 0
}

function chunk(type, data) {
  const len = Buffer.alloc(4)
  len.writeUInt32BE(data.length, 0)
  const typeBuf = Buffer.from(type, 'ascii')
  const crcBuf = Buffer.alloc(4)
  crcBuf.writeUInt32BE(crc32(Buffer.concat([typeBuf, data])), 0)
  return Buffer.concat([len, typeBuf, data, crcBuf])
}

function encodePNG(width, height, rgba) {
  const sig = Buffer.from([137, 80, 78, 71, 13, 10, 26, 10])
  const ihdrData = Buffer.alloc(13)
  ihdrData.writeUInt32BE(width, 0)
  ihdrData.writeUInt32BE(height, 4)
  ihdrData[8] = 8 // bit depth
  ihdrData[9] = 6 // color type RGBA
  ihdrData[10] = 0
  ihdrData[11] = 0
  ihdrData[12] = 0

  // add filter byte (0) per scanline
  const stride = width * 4
  const raw = Buffer.alloc((stride + 1) * height)
  for (let y = 0; y < height; y++) {
    raw[y * (stride + 1)] = 0
    rgba.copy(raw, y * (stride + 1) + 1, y * stride, y * stride + stride)
  }
  const idatData = deflateSync(raw, { level: 9 })

  return Buffer.concat([
    sig,
    chunk('IHDR', ihdrData),
    chunk('IDAT', idatData),
    chunk('IEND', Buffer.alloc(0)),
  ])
}

function makeIcon(size, { padding = 0 } = {}) {
  const rgba = Buffer.alloc(size * size * 4)
  const r = size * 0.22 // corner radius for rounded-square background
  const cx = size / 2
  const cy = size / 2
  const contentR = size / 2 - padding
  const ringOuter = contentR * 0.62
  const ringInner = contentR * 0.46
  const dotR = contentR * 0.13
  // dot sits on the ring at ~45deg (top-right), like a progress marker
  const dotAngle = -Math.PI / 4
  const dotCx = cx + ringOuter * 0.82 * Math.cos(dotAngle)
  const dotCy = cy + ringOuter * 0.82 * Math.sin(dotAngle)

  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      const i = (y * size + x) * 4
      // rounded-square mask
      const dx = Math.max(0, Math.max(r - x, x - (size - r)) )
      const dy = Math.max(0, Math.max(r - y, y - (size - r)))
      let inside = true
      if (x < r && y < r) inside = (x - r) ** 2 + (y - r) ** 2 <= r * r
      else if (x > size - r && y < r) inside = (x - (size - r)) ** 2 + (y - r) ** 2 <= r * r
      else if (x < r && y > size - r) inside = (x - r) ** 2 + (y - (size - r)) ** 2 <= r * r
      else if (x > size - r && y > size - r) inside = (x - (size - r)) ** 2 + (y - (size - r)) ** 2 <= r * r

      let color = inside ? BG : [0, 0, 0]
      let alpha = inside ? 255 : 0

      const dist = Math.hypot(x - cx, y - cy)
      const onRing = dist <= ringOuter && dist >= ringInner
      const onDot = Math.hypot(x - dotCx, y - dotCy) <= dotR

      if (inside && (onRing || onDot)) {
        color = FG
      }

      rgba[i] = color[0]
      rgba[i + 1] = color[1]
      rgba[i + 2] = color[2]
      rgba[i + 3] = alpha
      void dx; void dy
    }
  }
  return encodePNG(size, size, rgba)
}

writeFileSync(join(outDir, 'icon-192.png'), makeIcon(192))
writeFileSync(join(outDir, 'icon-512.png'), makeIcon(512))
writeFileSync(join(outDir, 'icon-512-maskable.png'), makeIcon(512, { padding: 64 }))
writeFileSync(join(outDir, 'apple-touch-icon.png'), makeIcon(180))
console.log('Icons generated in public/')
