import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Cardreceta } from './cardreceta';

describe('Cardreceta', () => {
  let component: Cardreceta;
  let fixture: ComponentFixture<Cardreceta>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Cardreceta]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Cardreceta);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
