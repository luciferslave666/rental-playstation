// components/Timer.tsx
"use client";

import { useState, useEffect } from 'react';

// 1. Definisikan tipe untuk props
interface TimerProps {
  startTime: string; // Kita tahu 'waktuMulai' adalah string
}

// Fungsi untuk format durasi HH:MM:SS
function formatDuration(seconds: number): string {
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  const s = Math.floor(seconds % 60);
  
  const pad = (num: number) => num.toString().padStart(2, '0');
  
  return `${pad(h)}:${pad(m)}:${pad(s)}`;
}

// 2. Gunakan 'TimerProps' di sini
export default function Timer({ startTime }: TimerProps) {
  // 3. Beri tipe data pada state (TS bisa menebak ini 'number')
  const [duration, setDuration] = useState<number>(0);

  useEffect(() => {
    const start = new Date(startTime);

    const now = new Date();
    const initialDuration = Math.floor((now.getTime() - start.getTime()) / 1000);
    setDuration(initialDuration);

    const interval = setInterval(() => {
      // TS akan memastikan kita selalu meng-set 'number'
      setDuration(prevDuration => prevDuration + 1);
    }, 1000);

    return () => clearInterval(interval);
  }, [startTime]);

  return (
    <div className="text-2xl font-mono text-yellow-300">
      {formatDuration(duration)}
    </div>
  );
}