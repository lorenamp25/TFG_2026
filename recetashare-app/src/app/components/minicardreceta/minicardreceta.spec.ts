import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Minicardreceta } from './minicardreceta';

describe('Minicardreceta', () => {
  let component: Minicardreceta;
  let fixture: ComponentFixture<Minicardreceta>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Minicardreceta]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Minicardreceta);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
